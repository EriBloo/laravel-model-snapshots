<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Models;

use Carbon\Carbon;
use EriBloo\LaravelModelSnapshots\Contracts\SnapshotInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property-read int $id
 * @property int $model_id
 * @property string $model_type
 * @property Model $snapshot
 * @property string $snapshot_version
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
class Snapshot extends Model implements SnapshotInterface
{
    /**
     * @var string
     */
    protected $table = 'model_snapshots';

    /**
     * @return Attribute
     */
    public function snapshot(): Attribute
    {
        return Attribute::make(
            get: static function (string $value, $attributes): Model {
                /** @var Model $model */
                $model = new $attributes['subject_type']();
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
     * @return Model
     */
    public function getSnapshotModel(): Model
    {
        return $this->snapshot;
    }

    /**
     * @param Model $model
     * @return void
     */
    public function setSnapshotModel(Model $model): void
    {
        $this->snapshot = $model;
    }

    /**
     * @return string
     */
    public function getSnapshotVersion(): string
    {
        return $this->snapshot_version;
    }

    /**
     * @param string $version
     * @return void
     */
    public function setSnapshotVersion(string $version): void
    {
        $this->snapshot_version = $version;
    }

    /**
     * @return MorphTo
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
