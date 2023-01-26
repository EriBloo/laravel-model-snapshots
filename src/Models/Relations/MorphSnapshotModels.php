<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Models\Relations;

use EriBloo\LaravelModelSnapshots\Contracts\SnapshotInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class MorphSnapshotModels extends MorphToMany
{
    /**
     * @param  string  $snapshotClass
     * @param  Model  $parent
     */
    public function __construct(protected string $snapshotClass, Model $parent)
    {
        parent::__construct(
            query: config('model-snapshots.snapshot_class')::query(),
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
     */
    public function addEagerConstraints(array $models): void
    {
        parent::addEagerConstraints($models);

        $this->query->where('model_snapshots.model_type', $this->snapshotClass);
    }

    /**
     * {@inheritDoc}
     */
    public function get($columns = ['*']): Collection
    {
        return parent::get(['model_snapshots.model_type', 'model_snapshots.snapshot'])
            ->map(fn (SnapshotInterface $snapshot) => $snapshot->getSnapshotModel());
    }
}
