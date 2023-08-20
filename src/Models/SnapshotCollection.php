<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Models;

use EriBloo\LaravelModelSnapshots\Contracts\Snapshot as SnapshotContract;
use Illuminate\Database\Eloquent\Collection;

class SnapshotCollection extends Collection
{
    public function toModels(bool $fillExcludedAttributes = false): SnapshotCollection
    {
        if ($fillExcludedAttributes) {
            $this->loadMissing('subject');
        }

        return new SnapshotCollection(
            $this->map(
                fn (SnapshotContract $snapshot) => $snapshot->toModel($fillExcludedAttributes)
            )
        );
    }
}
