<?php

declare(strict_types=1);

use EriBloo\LaravelModelSnapshots\Models\Snapshot;
use EriBloo\LaravelModelSnapshots\Tests\TestSupport\Models\TestCreatesSnapshotsModel;
use EriBloo\LaravelModelSnapshots\Tests\TestSupport\Models\TestHasSnapshotRelationsModel;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->now = Carbon::now()->toImmutable();
    Carbon::setTestNow($this->now);

    $this->attributes = TestCreatesSnapshotsModel::factory()->raw();
    $this->model = TestCreatesSnapshotsModel::query()->create($this->attributes);
});

it('creates snapshot', function () {
    $this->model->createSnapshot();
    $snapshot = $this->model->getLatestSnapshot();

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
    expect($this->model->getLatestSnapshot()->getSnapshotVersion())
        ->toBe('1');

    Carbon::setTestNow($this->now->addSecond());

    $this->model->createSnapshot();
    expect($this->model->getLatestSnapshot()->getSnapshotVersion())
        ->toBe('2');
});

it('creates proper relations with snapshots', function () {
    /** @var TestHasSnapshotRelationsModel $test */
    $test = TestHasSnapshotRelationsModel::create(['name' => 'Test']);
    $this->model->createSnapshot();

    $snapshot = $this->model->getLatestSnapshot();
    $test->testCreatesSnapshotsModels()->attach($snapshot->id);
    expect($test->testCreatesSnapshotsModels()->first())
        ->id->toBe($snapshot->snapshot->id)
        ->name->toBe($snapshot->snapshot->name)
        ->content->toBe($snapshot->snapshot->content)
        ->and($test->testCreatesSnapshots()->first())
        ->toBeInstanceOf(Snapshot::class)
        ->snapshot_version->toBe('1');
});

it('returns correct snapshots by version and date', function () {
    for ($i = 1; $i <= 10; $i++) {
        $this->model->createSnapshot();
        Carbon::setTestNow($this->now->addMinutes(10 * $i));
    }

    expect($this->model->getSnapshotByVersion('7'))
        ->snapshot_version->toBe('7')
        ->created_at->toDateTimeString()->toBe(Carbon::make($this->now->addMinutes(10 * 6))?->toDateTimeString())
        ->and($this->model->getSnapshotByDate($this->now->addMinutes(10 * 4 + 5)))
        ->snapshot_version->toBe('5')
        ->created_at->toDateTimeString()->toBe(Carbon::make($this->now->addMinutes(10 * 4))?->toDateTimeString());
});