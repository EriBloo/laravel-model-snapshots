<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots;

use Closure;
use EriBloo\LaravelModelSnapshots\Contracts\SnapshotInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\Conditionable;
use Spatie\Macroable\Macroable;

class Snapshotter
{
    use Conditionable;
    use Macroable;

    /**
     * @var SnapshotOptions
     */
    protected SnapshotOptions $options;

    /**
     * @var SnapshotInterface
     */
    protected SnapshotInterface $snapshot;

    /**
     * @param Model $model
     */
    public function __construct(protected Model $model)
    {
        $this->options = method_exists($this->model, 'getSnapshotOptions') ? $this->model->getSnapshotOptions() : SnapshotOptions::defaults();
        $this->snapshot = app(SnapshotInterface::class);
    }

    /**
     * @param SnapshotOptions|Closure $options
     * @return $this
     */
    public function usingOptions(SnapshotOptions|Closure $options): static
    {
        $this->options = $options instanceof Closure ? $options($this->options) : $options;

        return $this;
    }

    /**
     * @return SnapshotInterface
     */
    public function persist(): SnapshotInterface
    {
        $this->snapshot->subject()->associate($this->model);
        $this->setSnapshotVersion();
        $this->setSnapshotValue();

        $this->snapshot->save();

        return $this->snapshot;
    }

    /**
     * @return void
     */
    protected function setSnapshotVersion(): void
    {
        $currentVersion = $this->getLatestVersion();
        $versionist = $this->options->versionist;

        $this->snapshot->setSnapshotVersion($currentVersion ? $versionist->getNextVersion($currentVersion) : $versionist->getFirstVersion());
    }

    /**
     * Returns last snapshot version.
     *
     * @return string|null
     */
    protected function getLatestVersion(): string|null
    {
        /** @var SnapshotInterface|null $snapshot */
        $snapshot = $this->snapshot
            ->newQuery()
            ->whereMorphedTo($this->snapshot->subject(), $this->model->getMorphClass())
            ->latest()
            ->first();

        return $snapshot?->getSnapshotVersion();
    }

    /**
     * @return void
     */
    protected function setSnapshotValue(): void
    {
        $this->snapshot->setSnapshotValue($this->transformedModel());
    }

    /**
     * @return Model
     */
    protected function transformedModel(): Model
    {
        $replicate = $this->model->replicate($this->options->snapshotExcept);
        if ($this->options->snapshotHidden) {
            $replicate->setHidden([]);
        }

        return $replicate;
    }
}
