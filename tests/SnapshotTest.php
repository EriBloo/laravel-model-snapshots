<?php

declare(strict_types=1);

use EriBloo\LaravelModelSnapshots\Contracts\Snapshot as SnapshotContract;
use EriBloo\LaravelModelSnapshots\Models\Snapshot;
use EriBloo\LaravelModelSnapshots\Tests\TestSupport\Models\Document;
use EriBloo\LaravelModelSnapshots\Tests\TestSupport\Models\DocumentConsumer;
use EriBloo\LaravelModelSnapshots\Tests\TestSupport\Models\DocumentWithCasts;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->now = Carbon::now()->toImmutable();
    Carbon::setTestNow($this->now);

    $this->attributes = Document::factory()->raw();
    $this->model = Document::query()->create($this->attributes);
});

it('creates snapshot', function () {
    snapshot($this->model)->commit();
    $snapshot = $this->model->getLatestSnapshot();

    expect($snapshot)
        ->getAttribute('subject_id')->toBe($this->model->id)
        ->getAttribute('subject_type')->toBe($this->model->getMorphClass())
        ->and($snapshot?->toModel())
        ->getAttribute('name')->toBe($this->model->getAttribute('name'))
        ->getAttribute('content')->toBe($this->model->getAttribute('content'));
});

it('does not create duplicate snapshots when no changes were made', function () {
    snapshot($this->model)->commit();
    $first = $this->model->getLatestSnapshot();
    Carbon::setTestNow($this->now->addSecond());
    snapshot($this->model)->commit();
    $second = $this->model->getLatestSnapshot();

    expect($first)->is($second)->toBeTrue();
});

it('stores proper raw values', function () {
    /** @var DocumentWithCasts $model */
    $model = DocumentWithCasts::query()->create([
        'date_attr' => Carbon::now(),
        'array_attr' => ['test' => 'test', 'test'],
        'int_enum_attr' => \EriBloo\LaravelModelSnapshots\Tests\TestSupport\Enums\IntBackedEnum::one,
        'string_enum_attr' => \EriBloo\LaravelModelSnapshots\Tests\TestSupport\Enums\StringBackedEnum::two,
        'accessor_attr' => 'test',
        'mutator_attr' => 'test',
        'both_attr' => 'test',
    ]);

    snapshot($model)->commit();
    /** @var SnapshotContract $snapshot */
    $snapshot = $model->getLatestSnapshot()?->toModel();

    foreach ($model->getAttributes() as $property => $value) {
        if ($property === $model->getKeyName()) {
            continue;
        }
        expect($value)->toEqual($snapshot->getAttributes()[$property]);
    }
});

it('versions properly', function () {
    snapshot($this->model)->commit();
    expect($this->model->getLatestSnapshot()?->getAttribute('version'))
        ->toBe('1');

    Carbon::setTestNow($this->now->addSecond());
    $this->model->update(['name' => Str::random()]);

    snapshot($this->model)->commit();
    expect($this->model->getLatestSnapshot()?->getAttribute('version'))
        ->toBe('2');
});

it('creates proper relations with snapshots', function () {
    /** @var DocumentConsumer $test */
    $test = DocumentConsumer::create(['name' => 'Test']);
    snapshot($this->model)->commit();

    $snapshot = $this->model->getLatestSnapshot();
    $test->documentSnapshotValues()->attach($snapshot?->id);
    expect($test->documentSnapshotValues()->first())
        ->getAttribute('name')->toBe($snapshot?->toModel()->getAttribute('name'))
        ->getAttribute('content')->toBe($snapshot?->toModel()->getAttribute('content'))
        ->and($test->documentSnapshots()->first())
        ->toBeInstanceOf(Snapshot::class)
        ->getAttribute('version')->toBe('1')
        ->and($test->documentSnapshotValue()->first())
        ->getAttribute('name')->toBe($snapshot?->toModel()->getAttribute('name'))
        ->getAttribute('content')->toBe($snapshot?->toModel()->getAttribute('content'))
        ->and($test->documentSnapshot()->first())
        ->toBeInstanceOf(Snapshot::class)
        ->getAttribute('version')->toBe('1');
});

it('returns correct snapshots by version and date', function () {
    for ($i = 1; $i <= 10; $i++) {
        snapshot($this->model)->commit();
        Carbon::setTestNow($this->now->addMinutes(10 * $i));
        $this->model->update(['name' => Str::random()]);
    }

    expect($this->model->getSnapshotByVersion('7'))
        ->getAttribute('version')->toBe('7')
        ->getAttribute('created_at')->toDateTimeString()->toBe(Carbon::make($this->now->addMinutes(10 * 6))?->toDateTimeString())
        ->and($this->model->getSnapshotByDate($this->now->addMinutes(10 * 4 + 5)))
        ->getAttribute('version')->toBe('5')
        ->getAttribute('created_at')->toDateTimeString()->toBe(Carbon::make($this->now->addMinutes(10 * 4))?->toDateTimeString());
});

it('properly restores model', function () {
    snapshot($this->model)->commit();

    $this->model->update(Document::factory()->raw());

    expect($this->model->toArray())->not()->toMatchArray($this->attributes);

    /** @var Snapshot $snapshot */
    $snapshot = $this->model->getLatestSnapshot();
    $snapshot->restore();
    $this->model->refresh();

    expect($this->model->toArray())->toMatchArray($this->attributes);
});

it('properly restores as new model', function () {
    snapshot($this->model)->commit();
    Carbon::setTestNow($this->now->addSecond());
    $this->model->getLatestSnapshot()->restoreAsNew();

    expect($this->model)->is($this->model->newQuery()->latest()->first())->toBeFalse();
});

it('properly restores as new model with snapshots', function () {
    snapshot($this->model)->commit();
    Carbon::setTestNow($this->now->addSecond());
    $this->model->getLatestSnapshot()->restoreAsNew(true);

    expect($this->model->newQuery()->latest()->first()?->getLatestSnapshot())->not()->toBeFalsy();
});
