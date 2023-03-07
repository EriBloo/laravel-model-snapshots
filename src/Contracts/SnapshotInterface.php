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
    public function subject(): MorphTo;

    public function getSnapshotValue(): mixed;

    public function setSnapshotValue(Model $model): void;

    public function getSnapshotVersion(): string;

    public function setSnapshotVersion(string $version): void;

    public function getSnapshotOptions(): array;

    public function setSnapshotOptions(SnapshotOptions $options): void;
}
