<?php

declare(strict_types=1);

// config for EriBloo/LaravelModelSnapshots

return [

    /**
     * Snapshot class used. Must implement EriBloo\LaravelModelSnapshots\Contracts\SnapshotInterface interface.
     */
    'snapshot_class' => EriBloo\LaravelModelSnapshots\Models\Snapshot::class,

    /**
     * Versionist class used. Must implement EriBloo\LaravelModelSnapshots\Contracts\VersionistInterface interface.
     */
    'versionist_class' => EriBloo\LaravelModelSnapshots\Support\Versionists\IncrementingVersionist::class,

];
