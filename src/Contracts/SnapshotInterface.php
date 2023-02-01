<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Contracts;

use Illuminate\Database\Eloquent\Model;

interface SnapshotInterface
{
    /**
     * @param  Model  $model
     * @param  string  $version
     * @return Model
     */
    public static function newSnapshotForModel(Model $model, string $version): Model;

    /**
     * @return Model
     */
    public function getSnapshotModel(): Model;

    /**
     * @return string
     */
    public function getSnapshotVersion(): string;
}
