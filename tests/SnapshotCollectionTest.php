<?php

declare(strict_types=1);

use EriBloo\LaravelModelSnapshots\Models\SnapshotCollection;
use EriBloo\LaravelModelSnapshots\Tests\TestSupport\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->now = Carbon::now()->toImmutable();
    Carbon::setTestNow($this->now);

    $this->attributes = Document::factory()->raw();
    $this->model = Document::query()->create($this->attributes);
});

it('properly maps to models', function () {
    for ($i = 0; $i < 10; $i++) {
        snapshot($this->model)->forceDuplicate()->persist();
    }

    $collection = $this->model->snapshots()->get();

    expect($collection)->toBeInstanceOf(SnapshotCollection::class);
    $collection->toModels()->each(fn (Model $model) => expect($model)->toBeInstanceOf(Document::class));
});
