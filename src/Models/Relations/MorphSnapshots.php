<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Models\Relations;

use EriBloo\LaravelModelSnapshots\Models\Snapshot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class MorphSnapshots extends MorphToMany
{
    public function __construct(protected string $snapshotClass, Model $parent)
    {
        parent::__construct(
            query:Snapshot::query(),
            parent:$parent,
            name:'model',
            table:'model_snapshots_relations',
            foreignPivotKey: 'model_id',
            relatedPivotKey: 'snapshot_id',
            parentKey: 'id',
            relatedKey: 'id'
        );
    }

    public function addEagerConstraints(array $models): void
    {
        parent::addEagerConstraints($models);

        $this->query->where('model_snapshots.model_type', $this->snapshotClass);
    }

    public function get($columns = ['*'])
    {
        return parent::get(['model_snapshots.model_type', 'model_snapshots.snapshot'])
            ->map(fn (Snapshot $snapshot) => $snapshot->snapshot);
    }
}
