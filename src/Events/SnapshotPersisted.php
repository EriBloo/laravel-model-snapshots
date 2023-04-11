<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Events;

use EriBloo\LaravelModelSnapshots\Contracts\SnapshotInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SnapshotPersisted
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly SnapshotInterface $snapshot,
        public readonly Model $model,
    ) {
    }
}
