<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Models;

use Carbon\Carbon;
use EriBloo\LaravelModelSnapshots\Contracts\SnapshotInterface;
use EriBloo\LaravelModelSnapshots\Events\SnapshotRestored;
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
            get: static function (string $value): array {
                return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
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

    public function toModel(bool $fillExcludedAttributes = false): Model
    {
        /** @var Model $model */
        $model = $fillExcludedAttributes ?
            $this->subject()->firstOrFail()->replicate() : new ($this->getAttribute('subject_type'));
        $model->setRawAttributes($this->getAttribute('stored_attributes'));

        return $model;
    }

    public function restore(): Model
    {
        $model = $this->subject()->firstOrFail();
        $model->setRawAttributes($this->getAttribute('stored_attributes'));
        $model->save();

        event(new SnapshotRestored($this, $model));

        return $model;
    }

    public function restoreAsNew(bool $duplicateSnapshotHistory = false): Model
    {
        $original = $this->subject()->firstOrFail();
        $model = $original->replicate();
        $model->setRawAttributes($this->getAttribute('stored_attributes'));
        $model->save();

        if ($duplicateSnapshotHistory) {
            $this->newQuery()
                ->whereMorphedTo($this->subject(), $original->getMorphClass())
                ->whereDate(self::CREATED_AT, '<=', $this->getAttribute(self::CREATED_AT))
                ->get()
                ->each(function (self $snapshot) use ($model) {
                    $replicate = $snapshot->replicate();
                    $replicate->subject()->associate($model);
                    $replicate->save();
                });
        }

        return $model;
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
