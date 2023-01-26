<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Concerns;

use EriBloo\LaravelModelSnapshots\Contracts\Snapshot as SnapshotInterface;
use EriBloo\LaravelModelSnapshots\Contracts\Versionist as VersionistInterface;
use EriBloo\LaravelModelSnapshots\Models\Snapshot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin Model
 */
trait CreatesSnapshots
{
    /**
     * @return SnapshotInterface
     */
    public function createSnapshot(): SnapshotInterface
    {
        /** @var class-string<SnapshotInterface> $snapshotClass */
        $snapshotClass = $this->getSnapshotClass();
        $currentSnapshot = $this->getLatestSnapshot();
        $currentVersion = $currentSnapshot?->getSnapshotVersion();
        $versionist = $this->getVersionist();

        $snapshot = $snapshotClass::newSnapshotForModel(
            $this,
            $currentVersion ? $versionist->getNextVersion($currentVersion) : $versionist->getFirstVersion()
        );
        $this->snapshots()->save($snapshot);

        return $snapshot;
    }

    /**
     * @return class-string<SnapshotInterface>
     */
    protected function getSnapshotClass(): string
    {
        return config('model-snapshots.snapshot_class', Snapshot::class);
    }

    /**
     * Returns latest snapshot.
     *
     * @return SnapshotInterface|null
     */
    public function getLatestSnapshot(): SnapshotInterface|null
    {
        /** @var SnapshotInterface|null $snapshot */
        $snapshot = $this->snapshots()->latest()->first();

        return $snapshot;
    }

    /**
     * Get Versionist responsible for versioning
     *
     * @return VersionistInterface
     */
    public function getVersionist(): VersionistInterface
    {
        return app(VersionistInterface::class);
    }

    /**
     * @return MorphMany
     */
    public function snapshots(): MorphMany
    {
        return $this->morphMany($this->getSnapshotClass(), 'model');
    }
}
