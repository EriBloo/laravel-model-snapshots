<?php

declare(strict_types=1);

use EriBloo\LaravelModelSnapshots\Tests\TestSupport\Models\TestModel;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->now = now();
    Carbon::setTestNow($this->now);

    $this->attributes = TestModel::factory()->raw();
    $this->model = TestModel::query()->create($this->attributes);
});

it('creates snapshot', function () {
    $this->model->createSnapshot();

    $snapshot = $this->model->getSnapshot();
    expect($snapshot)
        ->model_id->toBe($this->model->id)
        ->model_type->toBe($this->model::class)
        ->and($snapshot->snapshot)
        ->id->toBe($this->model->id)
        ->name->toBe($this->model->name)
        ->content->toBe($this->model->content);
});

it('versions properly', function () {
    $this->model->createSnapshot();
    expect($this->model->getSnapshot()->snapshot_version)
        ->toBe('1');

    Carbon::setTestNow($this->now->addSecond());

    $this->model->createSnapshot();
    expect($this->model->getSnapshot()->snapshot_version)
        ->toBe('2');
});
