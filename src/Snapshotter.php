<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots;

use Closure;
use EriBloo\LaravelModelSnapshots\Contracts\SnapshotInterface;
use EriBloo\LaravelModelSnapshots\Contracts\VersionistInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\Conditionable;
use Spatie\Macroable\Macroable;

class Snapshotter
{
    use Conditionable;
    use Macroable;

    protected VersionistInterface $versionist;

    protected SnapshotInterface $snapshot;

    public function __construct(protected Model $model)
    {
        $this->versionist = app(VersionistInterface::class);
        $this->snapshot = app(SnapshotInterface::class);
        $this->snapshot->subject()->associate($this->model);
        $this->snapshot->setSnapshotValue($this->model);
    }

    /**
     * @param VersionistInterface|Closure $versionist
     * @return $this
     */
    public function usingVersionist(VersionistInterface|Closure $versionist): static
    {
        $this->versionist = $versionist instanceof Closure ? $versionist($this->versionist) : $versionist;

        return $this;
    }

    public function persist(): SnapshotInterface
    {
        $this->setSnapshotVersion();

        $this->snapshot->save();

        return $this->snapshot;
    }

    protected function setSnapshotVersion(): void
    {
        $currentVersion = $this->getLatestVersion();
        $this->snapshot->setSnapshotVersion($currentVersion ? $this->versionist->getNextVersion($currentVersion) : $this->versionist->getFirstVersion());
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
            ->whereMorphedTo($this->snapshot->subject(), $this->model::class)
            ->latest()
            ->first();

        return $snapshot?->getSnapshotVersion();
    }
}
