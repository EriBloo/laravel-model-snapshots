<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Concerns;

use Closure;
use EriBloo\LaravelModelSnapshots\Contracts\Versionist as VersionistContract;
use EriBloo\LaravelModelSnapshots\Snapshotter;

/**
 * @mixin Snapshotter
 */
trait SnapshotterSetters
{
    /**
     * @param  Closure(VersionistContract): void  $closure
     */
    public function version(Closure $closure): static
    {
        $closure($this->options->versionist);

        return $this;
    }

    public function description(?string $description): static
    {
        $this->snapshot->setAttribute('description', $description);

        return $this;
    }

    public function setExcept(array $except): static
    {
        $this->options->snapshotExcept($except);

        return $this;
    }

    public function appendExcept(array $except): static
    {
        $this->options->snapshotExcept(array_merge($this->options->snapshotExcept, $except));

        return $this;
    }

    public function removeExcept(array $except): static
    {
        $this->options->snapshotExcept(array_diff($this->options->snapshotExcept, $except));

        return $this;
    }

    public function withoutHidden(): static
    {
        $this->options->snapshotHidden(false);

        return $this;
    }

    public function withHidden(): static
    {
        $this->options->snapshotHidden();

        return $this;
    }

    public function forceDuplicate(): static
    {
        $this->options->snapshotDuplicate();

        return $this;
    }

    public function noDuplicate(): static
    {
        $this->options->snapshotDuplicate(false);

        return $this;
    }
}
