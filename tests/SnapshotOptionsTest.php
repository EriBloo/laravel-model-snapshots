<?php

declare(strict_types=1);

use EriBloo\LaravelModelSnapshots\Support\Versionists\SemanticVersionist;
use EriBloo\LaravelModelSnapshots\Tests\TestSupport\Models\Document;
use EriBloo\LaravelModelSnapshots\Tests\TestSupport\Models\DocumentWithCustomOptions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->now = Carbon::now()->toImmutable();
    Carbon::setTestNow($this->now);

    $this->attributes = Document::factory()->raw();
    $this->model = DocumentWithCustomOptions::query()->create($this->attributes);
});

it('uses custom versionist', function () {
    snapshot($this->model)->commit();

    expect($this->model->getLatestSnapshot()->getAttribute('version'))->toBe('0.1');
});

it('excludes attributes', function () {
    snapshot($this->model)->commit();

    expect($this->model->getLatestSnapshot()->toModel()->getAttribute('name'))->toBeNull();
});

it('can snapshot hidden', function () {
    snapshot($this->model)->commit();

    expect($this->model->getLatestSnapshot()->toModel()->getAttribute('content'))->not()->toBeNull();
});

it('can force snapshotting duplicates', function () {
    snapshot($this->model)->commit();
    Carbon::setTestNow($this->now->addSecond());
    snapshot($this->model)->commit();

    expect($this->model->getLatestSnapshot()->getKey())->toBe(2);
});

it('properly sets options at runtime', function () {
    snapshot($this->model)->setExcept([])->withoutHidden()->commit();

    expect($this->model->getLatestSnapshot()->toModel())
        ->getAttribute('content')->toBeNull()
        ->getAttribute('name')->not()->toBeNull();
});

it('properly versions with versionist set at runtime', function () {
    snapshot($this->model)->commit();
    expect($this->model->getLatestSnapshot())
        ->getAttribute('version')->toBe('0.1');

    Carbon::setTestNow($this->now->addSeconds(1));
    $this->model->update(['name' => Str::random()]);

    snapshot($this->model)->commit();
    expect($this->model->getLatestSnapshot())
        ->getAttribute('version')->toBe('0.2');
    $this->model->update(['name' => Str::random()]);

    Carbon::setTestNow($this->now->addSeconds(2));
    $this->model->update(['name' => Str::random()]);

    snapshot($this->model)
        ->version(fn (SemanticVersionist $versionist) => $versionist->incrementMajor())
        ->commit();
    expect($this->model->getLatestSnapshot())
        ->getAttribute('version')->toBe('1.0');

    Carbon::setTestNow($this->now->addSeconds(3));
    $this->model->update(['name' => Str::random()]);

    snapshot($this->model)
        ->version(fn (SemanticVersionist $versionist) => $versionist->incrementMinor())
        ->commit();
    expect($this->model->getLatestSnapshot())
        ->getAttribute('version')->toBe('1.1');
});
