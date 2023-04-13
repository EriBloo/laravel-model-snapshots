<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots;

use Closure;
use EriBloo\LaravelModelSnapshots\Contracts\SnapshotInterface;
use EriBloo\LaravelModelSnapshots\Events\SnapshotPersisted;
use EriBloo\LaravelModelSnapshots\Exceptions\IncompatibleVersionist;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\Conditionable;
use Spatie\Macroable\Macroable;

class Snapshotter
{
    use Conditionable;
    use Macroable;

    protected SnapshotOptions $options;

    protected SnapshotInterface $snapshot;

    public function __construct(protected Model $model)
    {
        $this->options = method_exists($this->model, 'getSnapshotOptions') ? $this->model->getSnapshotOptions() : SnapshotOptions::defaults();
        $this->snapshot = app(SnapshotInterface::class);
    }

    /**
     * @param  SnapshotOptions|Closure(SnapshotOptions): SnapshotOptions  $options
     * @return $this
     */
    public function usingOptions(SnapshotOptions|Closure $options): static
    {
        $this->options = $options instanceof Closure ? $options($this->options) : $options;

        return $this;
    }

    public function description(?string $description): static
    {
        $this->snapshot->setAttribute('description', $description);

        return $this;
    }

    /**
     * @throws IncompatibleVersionist
     */
    public function persist(): SnapshotInterface
    {
        $this->snapshot->subject()->associate($this->model);
        $this->setSnapshotVersion();
        $this->setSnapshotValue();
        $this->setSnapshotOptions();

        if (! $this->options->snapshotDuplicate && $matchingSnapshot = $this->findMatchingSnapshot()) {
            $this->snapshot = $matchingSnapshot;
        } else {
            $this->snapshot->save();

            event(new SnapshotPersisted($this->snapshot, $this->model));
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
    protected function getLatestSnapshot(): SnapshotInterface|null
    {
        /** @var SnapshotInterface|null $snapshot */
        $snapshot = $this->snapshot
            ->newQuery()
            ->whereMorphedTo($this->snapshot->subject(), $this->model->getMorphClass())
            ->latest()
            ->first();

        return $snapshot;
    }

    protected function findMatchingSnapshot(): SnapshotInterface|null
    {
        /** @var SnapshotInterface|null $snapshot */
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
