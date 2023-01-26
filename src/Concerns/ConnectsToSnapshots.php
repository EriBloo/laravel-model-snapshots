<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Concerns;

use EriBloo\LaravelModelSnapshots\Models\Relations\MorphSnapshotModel;
use EriBloo\LaravelModelSnapshots\Models\Relations\MorphSnapshotModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @mixin Model
 */
trait ConnectsToSnapshots
{
    /**
     * @param  class-string  $snapshotClass
     * @return MorphSnapshotModels
     */
    public function morphSnapshotModels(string $snapshotClass): MorphSnapshotModels
    {
        return new MorphSnapshotModels($snapshotClass, $this);
    }

    /**
     * @param  class-string  $snapshotClass
     * @return MorphSnapshotModel
     */
    public function morphSnapshotModel(string $snapshotClass): MorphSnapshotModel
    {
        return new MorphSnapshotModel($snapshotClass, $this);
    }

    /**
     * @param  class-string  $snapshotClass
     * @return MorphToMany
     */
    public function morphSnapshots(string $snapshotClass): MorphToMany
    {
        return $this->morphToMany(config('model-snapshots.snapshot_class'), 'model', 'model_snapshots_relations')
            ->where('model_snapshots.model_type', $snapshotClass);
    }
}
