<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots;

use Closure;
use EriBloo\LaravelModelSnapshots\Contracts\SnapshotInterface;
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

    /**
     * @throws IncompatibleVersionist
     */
    public function persist(): SnapshotInterface
    {
        $this->snapshot->subject()->associate($this->model);
        $this->setSnapshotVersion();
        $this->setSnapshotValue();
        $this->setSnapshotOptions();

        $this->snapshot->save();

        return $this->snapshot;
    }

    /**
     * @throws IncompatibleVersionist
     */
    protected function setSnapshotVersion(): void
    {
        $latestSnapshot = $this->getLatestSnapshot();
        $versionist = $this->options->versionist;

        if ($latestSnapshot && ($previous = data_get($latestSnapshot->getSnapshotOptions(), 'versionist')) !== $versionist::class) {
            throw IncompatibleVersionist::make(
                $previous,
                $versionist::class
            );
        }

        $latestVersion = $latestSnapshot?->getSnapshotVersion();

        $this->snapshot->setSnapshotVersion(
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

    protected function setSnapshotValue(): void
    {
        $this->snapshot->setSnapshotValue($this->transformedModel());
    }

    protected function transformedModel(): Model
    {
        $replicate = $this->model->replicate($this->options->snapshotExcept);
        if ($this->options->snapshotHidden) {
            $replicate->setHidden([]);
        }
        $replicate->unsetRelations();

        return $replicate;
    }

    protected function setSnapshotOptions(): void
    {
        $this->snapshot->setSnapshotOptions($this->options);
    }
}
