<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Contracts;

use Illuminate\Database\Eloquent\Model;

interface SnapshotInterface
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
    public function getSnapshotModel(): Model;

    /**
     * @return string
     */
    public function getSnapshotVersion(): string;
}
