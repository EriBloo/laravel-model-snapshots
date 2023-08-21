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
     * Alter version set. Closure receives current Versionist.
     *
     * @param  Closure(VersionistContract): void  $closure
     */
    public function version(Closure $closure): static
    {
        $closure($this->options->versionist);

        return $this;
    }

    /**
     * Specify optional description o0n snapshot.
     *
     * @return $this
     */
    public function description(?string $description): static
    {
        $this->snapshot->setAttribute('description', $description);

        return $this;
    }

    /**
     * Override excluded attributes.
     *
     * @return $this
     */
    public function setExcept(array $except): static
    {
        $this->options->snapshotExcept($except);

        return $this;
    }

    /**
     * Add attributes to exclude.
     *
     * @return $this
     */
    public function appendExcept(array $except): static
    {
        $this->options->snapshotExcept(array_merge($this->options->snapshotExcept, $except));

        return $this;
    }

    /**
     * Remove attributes from exclude.
     *
     * @return $this
     */
    public function removeExcept(array $except): static
    {
        $this->options->snapshotExcept(array_diff($this->options->snapshotExcept, $except));

        return $this;
    }

    /**
     * Do not snapshot hidden attributes.
     *
     * @return $this
     */
    public function withoutHidden(): static
    {
        $this->options->snapshotHidden(false);

        return $this;
    }

    /**
     * Snapshot hidden attributes.
     *
     * @return $this
     */
    public function withHidden(): static
    {
        $this->options->snapshotHidden();

        return $this;
    }

    /**
     * Create snapshot even if duplicate already exists.
     *
     * @return $this
     */
    public function forceDuplicate(): static
    {
        $this->options->snapshotDuplicate();

        return $this;
    }

    /**
     * Do not create snapshot if duplicate is found.
     *
     * @return $this
     */
    public function noDuplicate(): static
    {
        $this->options->snapshotDuplicate(false);

        return $this;
    }
}
