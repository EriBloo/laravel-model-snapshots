<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Tests;

use EriBloo\LaravelModelSnapshots\LaravelModelSnapshotsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');

        $migration = include __DIR__.'/../database/migrations/create_model_snapshots_tables.php.stub';
        $migration->up();

        Schema::create('documents', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('content');
            $table->timestamps();
        });

        Schema::create('document_consumers', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            static fn (string $modelName) => 'EriBloo\\LaravelModelSnapshots\\Tests\\TestSupport\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelModelSnapshotsServiceProvider::class,
        ];
    }
}
