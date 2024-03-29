<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Relations;

use EriBloo\LaravelModelSnapshots\Models\Snapshot;
use EriBloo\LaravelModelSnapshots\Models\SnapshotCollection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class MorphSnapshotModels extends MorphToMany
{
    public function __construct(Model $parent)
    {
        parent::__construct(
            query: config('model-snapshots.snapshot_class', Snapshot::class)::query(),
            parent: $parent,
            name: 'model',
            table: 'model_snapshots_relations',
            foreignPivotKey: 'model_id',
            relatedPivotKey: 'snapshot_id',
            parentKey: 'id',
            relatedKey: 'id'
        );
    }

    /**
     * {@inheritDoc}
     *
     * @return Collection<int,Model>
     */
    public function get($columns = ['*']): Collection
    {
        /** @var SnapshotCollection $collection */
        $collection = parent::get(['model_snapshots.subject_type', 'model_snapshots.stored_attributes']);

        return $collection->toModels();
    }
}
