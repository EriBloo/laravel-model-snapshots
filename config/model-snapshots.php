<?php

declare(strict_types=1);

// config for EriBloo/LaravelModelSnapshots

return [

    /**
     * Snapshot class used. Must implement EriBloo\LaravelModelSnapshots\Contracts\Snapshot interface.
     */
    'snapshot_class' => EriBloo\LaravelModelSnapshots\Models\Snapshot::class,

    /**
     * Versionist class used. Must implement EriBloo\LaravelModelSnapshots\Contracts\Versionist interface.
     */
    'versionist_class' => EriBloo\LaravelModelSnapshots\Support\Versionists\IncrementingVersionist::class,

];
