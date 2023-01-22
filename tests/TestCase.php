<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Tests;

use EriBloo\LaravelModelSnapshots\LaravelModelSnapshotsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'EriBloo\\LaravelModelSnapshots\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelModelSnapshotsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-model-snapshots_table.php.stub';
        $migration->up();
        */
    }
}
