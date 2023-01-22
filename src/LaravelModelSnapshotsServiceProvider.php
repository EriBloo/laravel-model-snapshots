<?php

namespace EriBloo\LaravelModelSnapshots;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use EriBloo\LaravelModelSnapshots\Commands\LaravelModelSnapshotsCommand;

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
            ->hasViews()
            ->hasMigration('create_laravel-model-snapshots_table')
            ->hasCommand(LaravelModelSnapshotsCommand::class);
    }
}
