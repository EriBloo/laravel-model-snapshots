<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Models;

use Carbon\Carbon;
use EriBloo\LaravelModelSnapshots\Contracts\Snapshot as SnapshotContract;
use EriBloo\LaravelModelSnapshots\Events\SnapshotBranched;
use EriBloo\LaravelModelSnapshots\Events\SnapshotForked;
use EriBloo\LaravelModelSnapshots\Events\SnapshotReverted;
use EriBloo\LaravelModelSnapshots\SnapshotOptions;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;

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
class Snapshot extends Model implements SnapshotContract
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

    public function newCollection(array $models = []): SnapshotCollection
    {
        return new SnapshotCollection($models);
    }

    public function toModel(bool $fillExcludedAttributes = false): Model
    {
        if ($fillExcludedAttributes) {
            /** @var Model $model */
            $model = ($this->relationLoaded('subject')
                ? $this->getRelation('subject') : $this->subject()->firstOrFail()
            )->replicate();
        } else {
            /** @var Model $model */
            $model = new ($this->getAttribute('subject_type'));
        }

        $model->setRawAttributes($this->getAttribute('stored_attributes'));

        return $model;
    }

    public function revert(): Model
    {
        /** @var Model $model */
        $model = DB::transaction(function () {
            $model = $this->relationLoaded('subject') ? $this->getRelation('subject') : $this->subject()->firstOrFail();
            $model->setRawAttributes($this->getAttribute('stored_attributes'));
            $model->save();

            $this->newQuery()
                ->whereMorphedTo($this->subject(), $model->getMorphClass())
                ->where(self::CREATED_AT, '>', $this->getAttribute(self::CREATED_AT))
                ->each(function (self $snapshot) {
                    $snapshot->delete();
                });

            return $model;
        });

        event(new SnapshotReverted($this, $model));

        return $model;
    }

    public function branch(): Model
    {
        /** @var Model $model */
        $model = DB::transaction(function () {
            $model = ($this->relationLoaded('subject')
                ? $this->getRelation('subject') : $this->subject()->firstOrFail()
            )->replicate();
            $model->setRawAttributes($this->getAttribute('stored_attributes'));
            $model->save();

            $this->newQuery()
                ->whereMorphedTo($this->subject(), $model->getMorphClass())
                ->where(self::CREATED_AT, '<=', $this->getAttribute(self::CREATED_AT))
                ->each(function (self $snapshot) use ($model) {
                    $replicate = $snapshot->replicate();
                    $replicate->subject()->associate($model);
                    $replicate->save();
                });

            return $model;
        });

        event(new SnapshotBranched($this, $model));

        return $model;
    }

    public function fork(): Model
    {
        $model = ($this->relationLoaded('subject')
            ? $this->getRelation('subject') : $this->subject()->firstOrFail()
        )->replicate();
        $model->setRawAttributes($this->getAttribute('stored_attributes'));
        $model->save();

        event(new SnapshotForked($this, $model));

        return $model;
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
