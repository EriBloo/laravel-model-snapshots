<?php

declare(strict_types=1);

use EriBloo\LaravelModelSnapshots\Models\Snapshot;
use EriBloo\LaravelModelSnapshots\Tests\TestSupport\Models\Document;
use EriBloo\LaravelModelSnapshots\Tests\TestSupport\Models\DocumentConsumer;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->now = Carbon::now()->toImmutable();
    Carbon::setTestNow($this->now);

    $this->attributes = Document::factory()->raw();
    $this->model = Document::query()->create($this->attributes);
});

it('creates snapshot', function () {
    snapshot($this->model)->persist();
    $snapshot = $this->model->getLatestSnapshot();

    expect($snapshot)
        ->subject_id->toBe($this->model->id)
        ->subject_type->toBe($this->model::class)
        ->and($snapshot?->snapshot)
        ->id->toBe($this->model->id)
        ->name->toBe($this->model->name)
        ->content->toBe($this->model->content);
});

it('versions properly', function () {
    snapshot($this->model)->persist();
    expect($this->model->getLatestSnapshot()?->getSnapshotVersion())
        ->toBe('1');

    Carbon::setTestNow($this->now->addSecond());

    snapshot($this->model)->persist();
    expect($this->model->getLatestSnapshot()?->getSnapshotVersion())
        ->toBe('2');
});

it('creates proper relations with snapshots', function () {
    /** @var DocumentConsumer $test */
    $test = DocumentConsumer::create(['name' => 'Test']);
    snapshot($this->model)->persist();

    $snapshot = $this->model->getLatestSnapshot();
    $test->testCreatesSnapshotsModels()->attach($snapshot?->id);
    expect($test->testCreatesSnapshotsModels()->first())
        ->id->toBe($snapshot?->snapshot->id)
        ->name->toBe($snapshot?->snapshot->name)
        ->content->toBe($snapshot?->snapshot->content)
        ->and($test->testCreatesSnapshots()->first())
        ->toBeInstanceOf(Snapshot::class)
        ->snapshot_version->toBe('1')
        ->and($test->testCreatesSnapshotsModel()->first())
        ->id->toBe($snapshot?->snapshot->id)
        ->name->toBe($snapshot?->snapshot->name)
        ->content->toBe($snapshot?->snapshot->content);
});

it('returns correct snapshots by version and date', function () {
    for ($i = 1; $i <= 10; $i++) {
        snapshot($this->model)->persist();
        Carbon::setTestNow($this->now->addMinutes(10 * $i));
    }

    expect($this->model->getSnapshotByVersion('7'))
        ->snapshot_version->toBe('7')
        ->created_at->toDateTimeString()->toBe(Carbon::make($this->now->addMinutes(10 * 6))?->toDateTimeString())
        ->and($this->model->getSnapshotByDate($this->now->addMinutes(10 * 4 + 5)))
        ->snapshot_version->toBe('5')
        ->created_at->toDateTimeString()->toBe(Carbon::make($this->now->addMinutes(10 * 4))?->toDateTimeString());
});
