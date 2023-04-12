<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Relations;

use EriBloo\LaravelModelSnapshots\Contracts\SnapshotInterface;
use EriBloo\LaravelModelSnapshots\Models\Snapshot;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Concerns\SupportsDefaultModels;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class MorphSnapshotModel extends MorphToMany
{
    use SupportsDefaultModels;

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
     */
    public function initRelation(array $models, $relation): array
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->getDefaultFor($model));
        }

        return $models;
    }

    /**
     * {@inheritDoc}
     *
     * @return Collection<int,Model>
     */
    public function get($columns = ['*']): Collection
    {
        return parent::get(['model_snapshots.subject_type', 'model_snapshots.stored_attributes'])
            ->map(fn (SnapshotInterface $snapshot) => $snapshot->toModel());
    }

    /**
     * {@inheritDoc}
     */
    public function getResults()
    {
        return $this->first() ?: $this->getDefaultFor($this->getRelated());
    }

    /**
     * {@inheritDoc}
     */
    public function newRelatedInstanceFor(Model $parent): Model|Builder
    {
        return $this->related->newInstance();
    }
}
