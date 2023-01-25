<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Models;

use Carbon\Carbon;
use EriBloo\LaravelModelSnapshots\Contracts\Snapshot as SnapshotInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $model_id
 * @property string $model_type
 * @property Model $snapshot
 * @property string $snapshot_version
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @implements SnapshotInterface
 */
class Snapshot extends Model implements SnapshotInterface
{
    /**
     * @var string
     */
    protected $table = 'model_snapshots';

    /**
     * @param  Model  $model
     * @param  string  $version
     * @return static
     */
    public static function newSnapshotForModel(Model $model, string $version): static
    {
        $snapshot = new static;
        $snapshot->snapshot = $model;
        $snapshot->snapshot_version = $version;

        return $snapshot;
    }

    /**
     * @return Model
     */
    public function getModelSnapshot(): Model
    {
        return $this->snapshot;
    }

    /**
     * @return string
     */
    public function getSnapshotVersion(): string
    {
        return $this->snapshot_version;
    }

    /**
     * @return Attribute
     */
    public function snapshot(): Attribute
    {
        return Attribute::make(
            get: static function (string $value, $attributes): Model {
                /** @var Model $model */
                $model = new $attributes['model_type'];
                $model->forceFill(json_decode($value, true, 512, JSON_THROW_ON_ERROR));

                return $model;
            },
            set: static function (Model $model): string {
                $clone = clone $model;
                if (config('model-snapshots.should_snapshot_hidden')) {
                    $clone->setHidden([]);
                }

                return $clone->toJson();
            }
        );
    }

    /**
     * @return MorphTo
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
