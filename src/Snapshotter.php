<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots;

use EriBloo\LaravelModelSnapshots\Concerns\SnapshotterSetters;
use EriBloo\LaravelModelSnapshots\Contracts\Snapshot as SnapshotContract;
use EriBloo\LaravelModelSnapshots\Events\SnapshotCommitted;
use EriBloo\LaravelModelSnapshots\Exceptions\IncompatibleVersionist;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\Conditionable;
use Spatie\Macroable\Macroable;

class Snapshotter
{
    use Conditionable;
    use Macroable;
    use SnapshotterSetters;

    protected SnapshotOptions $options;

    protected SnapshotContract $snapshot;

    public function __construct(protected Model $model)
    {
        $this->options = method_exists($this->model, 'getSnapshotOptions') ?
            $this->model->getSnapshotOptions() : SnapshotOptions::defaults();
        $this->snapshot = app(SnapshotContract::class);
    }

    /**
     * Get currently set options.
     */
    public function getOptions(): SnapshotOptions
    {
        return $this->options;
    }

    /**
     * Persist snapshot to database.
     *
     * If duplicate is found and force option is not set returns found snapshot, otherwise returns persisted snapshot.
     *
     * @throws IncompatibleVersionist
     */
    public function commit(): SnapshotContract
    {
        $this->snapshot->subject()->associate($this->model);
        $this->setSnapshotVersion();
        $this->setSnapshotValue();
        $this->setSnapshotOptions();

        if (! $this->options->snapshotDuplicate && $matchingSnapshot = $this->findMatchingSnapshot()) {
            $this->snapshot = $matchingSnapshot;
        } else {
            $this->snapshot->save();

            event(new SnapshotCommitted($this->snapshot, $this->model));
        }

        return $this->snapshot;
    }

    /**
     * @throws IncompatibleVersionist
     */
    protected function setSnapshotVersion(): void
    {
        $latestSnapshot = $this->getLatestSnapshot();
        $versionist = $this->options->versionist;

        if ($latestSnapshot
            && ($previous = data_get($latestSnapshot->getAttribute('options'), 'versionist')) !== $versionist::class
        ) {
            throw IncompatibleVersionist::make(
                $previous,
                $versionist::class
            );
        }

        $latestVersion = $latestSnapshot?->getAttribute('version');

        $this->snapshot->setAttribute(
            'version',
            $latestVersion ? $versionist->getNextVersion($latestVersion) : $versionist->getFirstVersion()
        );
    }

    /**
     * Returns last snapshot.
     */
    protected function getLatestSnapshot(): SnapshotContract|null
    {
        /** @var SnapshotContract|null $snapshot */
        $snapshot = $this->snapshot
            ->newQuery()
            ->whereMorphedTo($this->snapshot->subject(), $this->model->getMorphClass())
            ->latest()
            ->first();

        return $snapshot;
    }

    protected function findMatchingSnapshot(): SnapshotContract|null
    {
        /** @var SnapshotContract|null $snapshot */
        $snapshot = $this->snapshot
            ->newQuery()
            ->whereMorphedTo($this->snapshot->subject(), $this->model->getMorphClass())
            ->where('stored_attributes', $this->snapshot->getAttribute('stored_attributes'))
            ->first();

        return $snapshot;
    }

    protected function setSnapshotValue(): void
    {
        $this->snapshot->setAttribute('stored_attributes', $this->transformedModel());
    }

    protected function transformedModel(): Model
    {
        $replicate = $this->model->replicate(
            array_merge($this->options->snapshotExcept, $this->options->snapshotHidden ? [] : $this->model->getHidden())
        );
        $replicate->unsetRelations();

        return $replicate;
    }

    protected function setSnapshotOptions(): void
    {
        $this->snapshot->setAttribute('options', $this->options);
    }
}
