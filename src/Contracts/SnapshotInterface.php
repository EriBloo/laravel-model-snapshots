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

    public function getSnapshot(): mixed;

    public function setSnapshot(Model $model): void;

    public function getVersion(): string;

    public function setVersion(string $version): void;

    public function getOptions(): array;

    public function setOptions(SnapshotOptions $options): void;
}
