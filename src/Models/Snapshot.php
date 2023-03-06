<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Models;

use Carbon\Carbon;
use EriBloo\LaravelModelSnapshots\Contracts\SnapshotInterface;
use EriBloo\LaravelModelSnapshots\SnapshotOptions;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property-read int $id
 * @property int $model_id
 * @property string $model_type
 * @property Model $value
 * @property string $version
 * @property SnapshotOptions|array $options;
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
    public function value(): Attribute
    {
        return Attribute::make(
            get: static function (string $value, $attributes): Model {
                /** @var Model $model */
                $model = new $attributes['subject_type']();
                $model->forceFill(json_decode($value, true, 512, JSON_THROW_ON_ERROR));

                return $model;
            },
            set: static function (Model $model): string {
                return $model->toJson();
            }
        );
    }

    /**
     * @return Attribute
     */
    public function options(): Attribute
    {
        return Attribute::make(
            get:static function (string $value): array {
                return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            },
            set: static fn (SnapshotOptions $options): string => json_encode([
                'versionist' => $options->versionist::class,
                'snapshot_except' => $options->snapshotExcept,
                'snapshot_hidden' => $options->snapshotHidden
            ], JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @return Model
     */
    public function getSnapshotValue(): Model
    {
        return $this->value;
    }

    /**
     * @param Model $model
     * @return void
     */
    public function setSnapshotValue(Model $model): void
    {
        $this->value = $model;
    }

    /**
     * @return string
     */
    public function getSnapshotVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     * @return void
     */
    public function setSnapshotVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return array
     */
    public function getSnapshotOptions(): array
    {
        return $this->options;
    }

    /**
     * @param SnapshotOptions $options
     * @return void
     */
    public function setSnapshotOptions(SnapshotOptions $options): void
    {
        $this->options = $options;
    }

    /**
     * @return MorphTo
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
