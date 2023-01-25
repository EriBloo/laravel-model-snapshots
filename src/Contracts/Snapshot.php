<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Contracts;

use Illuminate\Database\Eloquent\Model;

interface Snapshot
{
    /**
     * @param  Model  $model
     * @param  string  $version
     * @return static
     */
    public static function newSnapshotForModel(Model $model, string $version): static;

    /**
     * @return Model
     */
    public function getModelSnapshot(): Model;

    /**
     * @return string
     */
    public function getSnapshotVersion(): string;
}
