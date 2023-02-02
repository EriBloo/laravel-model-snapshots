<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin Model
 */
interface SnapshotInterface
{
    /**
     * @return MorphTo
     */
    public function subject(): MorphTo;

    /**
     * @return Model
     */
    public function getSnapshotModel(): Model;

    /**
     * @param Model $model
     * @return void
     */
    public function setSnapshotModel(Model $model): void;

    /**
     * @return string
     */
    public function getSnapshotVersion(): string;

    /**
     * @param string $version
     * @return void
     */
    public function setSnapshotVersion(string $version): void;
}
