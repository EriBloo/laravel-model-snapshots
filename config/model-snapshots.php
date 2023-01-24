<?php

declare(strict_types=1);

// config for EriBloo/LaravelModelSnapshots
use EriBloo\LaravelModelSnapshots\Models\Snapshot;
use EriBloo\LaravelModelSnapshots\Support\Versionists\IncrementingVersionist;

return [

    /**
     * Snapshot class used. Must implement EriBloo\LaravelModelSnapshots\Contracts\Snapshot interface.
     */
    'snapshot_class' => Snapshot::class,

    /**
     * Versionist class used. Must implement EriBloo\LaravelModelSnapshots\Contracts\Versionist interface.
     */
    'versionist_class' => IncrementingVersionist::class,

    /**
     * Determine if hidden attributes should be stored.
     */
    'should_snapshot_hidden' => true,

];
