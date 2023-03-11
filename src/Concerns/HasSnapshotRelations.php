<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Concerns;

use EriBloo\LaravelModelSnapshots\Relations\MorphSnapshotModel;
use EriBloo\LaravelModelSnapshots\Relations\MorphSnapshotModels;
use Fidum\EloquentMorphToOne\HasMorphToOne;
use Fidum\EloquentMorphToOne\MorphToOne;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @mixin Model
 */
trait HasSnapshotRelations
{
    use HasMorphToOne;

    /**
     * @param  class-string  $snapshotClass
     */
    public function morphSnapshotModels(string $snapshotClass): MorphSnapshotModels
    {
        return (new MorphSnapshotModels($this))->where('model_snapshots.subject_type', $snapshotClass);
    }

    /**
     * @param  class-string  $snapshotClass
     */
    public function morphSnapshotModel(string $snapshotClass): MorphSnapshotModel
    {
        return (new MorphSnapshotModel($this))->where('model_snapshots.subject_type', $snapshotClass);
    }

    /**
     * @param  class-string  $snapshotClass
     */
    public function morphSnapshots(string $snapshotClass): MorphToMany
    {
        return $this->morphToMany(config('model-snapshots.snapshot_class'), 'model', 'model_snapshots_relations')
            ->where('model_snapshots.subject_type', $snapshotClass);
    }

    /**
     * @param  class-string  $snapshotClass
     */
    public function morphSnapshot(string $snapshotClass): MorphToOne
    {
        return $this->morphToOne(config('model-snapshots.snapshot_class'), 'model', 'model_snapshots_relations')
            ->where('model_snapshots.subject_type', $snapshotClass);
    }
}
