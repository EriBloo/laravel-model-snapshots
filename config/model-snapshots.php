<?php

declare(strict_types=1);

// config for EriBloo/LaravelModelSnapshots
use EriBloo\LaravelModelSnapshots\Models\Snapshot;
use EriBloo\LaravelModelSnapshots\Support\Versionists\IncrementingVersionist;

return [

    /**
     * Snapshot class used. Must implement EriBloo\LaravelModelSnapshots\Contracts\SnapshotInterface interface.
     */
    'snapshot_class' => Snapshot::class,

    /**
     * Versionist class used. Must implement EriBloo\LaravelModelSnapshots\Contracts\VersionistInterface interface.
     */
    'versionist_class' => IncrementingVersionist::class,

];
