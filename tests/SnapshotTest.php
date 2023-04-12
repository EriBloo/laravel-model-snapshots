<?php

declare(strict_types=1);

use EriBloo\LaravelModelSnapshots\Contracts\SnapshotInterface;
use EriBloo\LaravelModelSnapshots\Exceptions\IncompatibleVersionist;
use EriBloo\LaravelModelSnapshots\Models\Snapshot;
use EriBloo\LaravelModelSnapshots\SnapshotOptions;
use EriBloo\LaravelModelSnapshots\Support\Versionists\SemanticVersionist;
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
    snapshot($this->model)->persist();
    $snapshot = $this->model->getLatestSnapshot();

    expect($snapshot)
        ->getAttribute('subject_id')->toBe($this->model->id)
        ->getAttribute('subject_type')->toBe($this->model->getMorphClass())
        ->and($snapshot?->toModel())
        ->getAttribute('name')->toBe($this->model->getAttribute('name'))
        ->getAttribute('content')->toBe($this->model->getAttribute('content'));
});

it('does not create duplicate snapshots when no changes were made', function () {
    snapshot($this->model)->persist();
    $first = $this->model->getLatestSnapshot();
    Carbon::setTestNow($this->now->addSecond());
    snapshot($this->model)->persist();
    $second = $this->model->getLatestSnapshot();

    expect($first)->getKey()->toBe($second->getKey());
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

    snapshot($model)->persist();
    /** @var SnapshotInterface $snapshot */
    $snapshot = $model->getLatestSnapshot()?->toModel();

    foreach ($model->getAttributes() as $property => $value) {
        if ($property === $model->getKeyName()) {
            continue;
        }
        expect($value)->toEqual($snapshot->getAttributes()[$property]);
    }
});

it('versions properly', function () {
    snapshot($this->model)->persist();
    expect($this->model->getLatestSnapshot()?->getAttribute('version'))
        ->toBe('1');

    Carbon::setTestNow($this->now->addSecond());
    $this->model->update(['name' => Str::random()]);

    snapshot($this->model)->persist();
    expect($this->model->getLatestSnapshot()?->getAttribute('version'))
        ->toBe('2');
});

it('creates proper relations with snapshots', function () {
    /** @var DocumentConsumer $test */
    $test = DocumentConsumer::create(['name' => 'Test']);
    snapshot($this->model)->persist();

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
        snapshot($this->model)->persist();
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

it('properly versions with versionist set at runtime', function () {
    $versionist = new SemanticVersionist();

    snapshot($this->model)->usingOptions(SnapshotOptions::defaults()->withVersionist($versionist))->persist();
    expect($this->model->getLatestSnapshot())
        ->getAttribute('version')->toBe('0.1.0');

    Carbon::setTestNow($this->now->addSeconds(1));
    $this->model->update(['name' => Str::random()]);

    snapshot($this->model)->usingOptions(SnapshotOptions::defaults()->withVersionist($versionist))->persist();
    expect($this->model->getLatestSnapshot())
        ->getAttribute('version')->toBe('0.2.0');
    $this->model->update(['name' => Str::random()]);

    Carbon::setTestNow($this->now->addSeconds(2));
    $this->model->update(['name' => Str::random()]);

    snapshot($this->model)->usingOptions(SnapshotOptions::defaults()->withVersionist($versionist->incrementMajor()))->persist();
    expect($this->model->getLatestSnapshot())
        ->getAttribute('version')->toBe('1.0.0');

    Carbon::setTestNow($this->now->addSeconds(3));
    $this->model->update(['name' => Str::random()]);

    snapshot($this->model)->usingOptions(SnapshotOptions::defaults()->withVersionist($versionist->incrementPatch()))->persist();
    expect($this->model->getLatestSnapshot())
        ->getAttribute('version')->toBe('1.0.1');

    Carbon::setTestNow($this->now->addSeconds(4));
    $this->model->update(['name' => Str::random()]);

    snapshot($this->model)->usingOptions(SnapshotOptions::defaults()->withVersionist($versionist->incrementMinor()))->persist();
    expect($this->model->getLatestSnapshot())
        ->getAttribute('version')->toBe('1.1.0');
});

it('throws when incompatible versionist is used', function () {
    snapshot($this->model)->persist();

    expect(function () {
        snapshot($this->model)
            ->usingOptions(SnapshotOptions::defaults()->withVersionist(new SemanticVersionist()))
            ->persist();
    })->toThrow(IncompatibleVersionist::class);
});

it('properly restores model', function () {
    snapshot($this->model)->persist();

    $this->model->update(Document::factory()->raw());

    expect($this->model->toArray())->not()->toMatchArray($this->attributes);

    /** @var SnapshotInterface $snapshot */
    $snapshot = $this->model->getLatestSnapshot();
    $snapshot->restore();
    $this->model->refresh();

    expect($this->model->toArray())->toMatchArray($this->attributes);
});
