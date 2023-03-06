<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Contracts;

use EriBloo\LaravelModelSnapshots\SnapshotOptions;
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
    public function getSnapshotValue(): mixed;

    /**
     * @param Model $model
     * @return void
     */
    public function setSnapshotValue(Model $model): void;

    /**
     * @return string
     */
    public function getSnapshotVersion(): string;

    /**
     * @param string $version
     * @return void
     */
    public function setSnapshotVersion(string $version): void;

    /**
     * @return array
     */
    public function getSnapshotOptions(): array;

    /**
     * @param SnapshotOptions $options
     * @return void
     */
    public function setSnapshotOptions(SnapshotOptions $options): void;
}
