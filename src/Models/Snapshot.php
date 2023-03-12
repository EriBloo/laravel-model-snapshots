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
 * @property Model $value
 * @property string $version
 * @property SnapshotOptions|array $options;
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
class Snapshot extends Model implements SnapshotInterface
{
    protected $table = 'model_snapshots';

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
            ], JSON_THROW_ON_ERROR)
        );
    }

    public function getSnapshotValue(): Model
    {
        return $this->value;
    }

    public function setSnapshotValue(Model $model): void
    {
        $this->value = $model;
    }

    public function getSnapshotVersion(): string
    {
        return $this->version;
    }

    public function setSnapshotVersion(string $version): void
    {
        $this->version = $version;
    }

    public function getSnapshotOptions(): array
    {
        return $this->options;
    }

    public function setSnapshotOptions(SnapshotOptions $options): void
    {
        $this->options = $options;
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
