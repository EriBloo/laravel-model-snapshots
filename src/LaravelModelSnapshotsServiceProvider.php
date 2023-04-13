<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots;

use EriBloo\LaravelModelSnapshots\Contracts\Snapshot as SnapshotContract;
use EriBloo\LaravelModelSnapshots\Contracts\Versionist as VersionistContract;
use EriBloo\LaravelModelSnapshots\Models\Snapshot;
use EriBloo\LaravelModelSnapshots\Support\Versionists\IncrementingVersionist;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelModelSnapshotsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-model-snapshots')
            ->hasConfigFile()
            ->hasMigration('create_model_snapshots_tables');
    }

    public function packageBooted(): void
    {
        $this->app->bind(
            VersionistContract::class,
            config('model-snapshots.versionist_class', IncrementingVersionist::class)
        );
        $this->app->bind(
            SnapshotContract::class,
            config('model-snapshots.snapshot_class', Snapshot::class)
        );
    }
}
