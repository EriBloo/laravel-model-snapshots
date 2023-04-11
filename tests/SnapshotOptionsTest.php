<?php

declare(strict_types=1);

use EriBloo\LaravelModelSnapshots\Tests\TestSupport\Models\Document;
use EriBloo\LaravelModelSnapshots\Tests\TestSupport\Models\DocumentWithCustomOptions;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->now = Carbon::now()->toImmutable();
    Carbon::setTestNow($this->now);

    $this->attributes = Document::factory()->raw();
    $this->model = DocumentWithCustomOptions::query()->create($this->attributes);
});

it('uses custom versionist', function () {
    snapshot($this->model)->persist();

    expect($this->model->getLatestSnapshot()->getVersion())->toBe('0.1.0');
});

it('excludes attributes', function () {
    snapshot($this->model)->persist();

    expect($this->model->getLatestSnapshot()->getSnapshot()->name)->toBeNull();
});

it('can snapshot hidden', function () {
    snapshot($this->model)->persist();

    expect($this->model->getLatestSnapshot()->getSnapshot()->content)->not()->toBeNull();
});

it('can force snapshotting duplicates', function () {
    snapshot($this->model)->persist();
    Carbon::setTestNow($this->now->addSecond());
    snapshot($this->model)->persist();

    expect($this->model->getLatestSnapshot()->getKey())->toBe(2);
});
