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
 * @property int $subject_id
 * @property string $subject_type
 * @property Model $stored_attributes
 * @property string $version
 * @property SnapshotOptions|array $options;
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
class Snapshot extends Model implements SnapshotInterface
{
    protected $table = 'model_snapshots';

    public function storedAttributes(): Attribute
    {
        return Attribute::make(
            get: static function (string $value, $attributes): Model {
                /** @var Model $model */
                $model = new $attributes['subject_type']();
                $model->setRawAttributes(json_decode($value, true, 512, JSON_THROW_ON_ERROR));

                return $model;
            },
            set: static function (Model $model): string {
                return json_encode($model->getAttributes(), JSON_THROW_ON_ERROR);
            }
        );
    }

    public function options(): Attribute
    {
        return Attribute::make(
            get: static function (string $value): array {
                return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            },
            set: static fn (SnapshotOptions $options): string => json_encode([
                'versionist' => $options->versionist::class,
                'snapshot_except' => $options->snapshotExcept,
                'snapshot_hidden' => $options->snapshotHidden,
                'snapshot_duplicate' => $options->snapshotDuplicate,
            ], JSON_THROW_ON_ERROR)
        );
    }

    public function getSnapshot(): Model
    {
        return $this->stored_attributes;
    }

    public function setSnapshot(Model $model): void
    {
        $this->stored_attributes = $model;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(SnapshotOptions $options): void
    {
        $this->options = $options;
    }

    public function restore(): Model
    {
        $model = $this->subject()->firstOrFail();

        $model->setRawAttributes($this->getSnapshot()->getAttributes());
        $model->save();

        return $model;
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
