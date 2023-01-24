<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Concerns;

use EriBloo\LaravelModelSnapshots\Contracts\Snapshot as SnapshotInterface;
use EriBloo\LaravelModelSnapshots\Contracts\Versionist;
use EriBloo\LaravelModelSnapshots\Models\Snapshot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin Model
 */
trait CreatesSnapshots
{
    /**
     * @return Snapshot
     */
    public function createSnapshot(): Snapshot
    {
        /** @var SnapshotInterface $snapshotClass */
        $snapshotClass = config('model-snapshots.snapshot_class');
        /** @var Snapshot|null $currentSnapshot */
        $currentSnapshot = $this->getSnapshot();
        $currentVersion = $currentSnapshot?->snapshot_version;
        $versionist = $this->getVersionist();

        $snapshot = $snapshotClass::newSnapshotForModel(
            $this,
            $currentVersion ? $versionist->getNextVersion($currentVersion) : $versionist->getFirstVersion()
        );
        $this->snapshots()->save($snapshot);

        return $snapshot;
    }

    /**
     * Returns last snapshot or snapshot by version.
     *
     * @param  string|null  $version
     * @return Snapshot|null
     */
    public function getSnapshot(string $version = null): Model|null
    {
        return $version ?
            $this->snapshots()->where('snapshot_version', $version)->first() :
            $this->snapshots()->latest()->first();
    }

    /**
     * @return Versionist
     */
    public function getVersionist(): Versionist
    {
        return app(Versionist::class);
    }

    /**
     * @return MorphMany
     */
    public function snapshots(): MorphMany
    {
        return $this->morphMany(config('model-snapshots.snapshot_class'), 'model');
    }
}
