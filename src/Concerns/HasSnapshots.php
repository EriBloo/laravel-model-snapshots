<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Concerns;

use Carbon\Carbon;
use DateTimeInterface;
use EriBloo\LaravelModelSnapshots\Contracts\SnapshotInterface;
use EriBloo\LaravelModelSnapshots\Models\Snapshot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin Model
 */
trait HasSnapshots
{
    public function snapshots(): MorphMany
    {
        return $this->morphMany(config('model-snapshots.snapshot_class', Snapshot::class), 'subject');
    }

    public function getLatestSnapshot(): SnapshotInterface|null
    {
        /** @var SnapshotInterface|null $snapshot */
        $snapshot = $this->snapshots()->latest()->first();

        return $snapshot;
    }

    public function getSnapshotByVersion(string $version): SnapshotInterface|null
    {
        /** @var SnapshotInterface|null $snapshot */
        $snapshot = $this->snapshots()->where('version', $version)->first();

        return $snapshot;
    }

    /**
     * Returns snapshot by date.
     */
    public function getSnapshotByDate(DateTimeInterface $date): SnapshotInterface|null
    {
        /** @var SnapshotInterface|null $snapshot */
        $snapshot = $this->snapshots()->latest()->where('created_at', '<=', Carbon::instance($date))->first();

        return $snapshot;
    }
}
