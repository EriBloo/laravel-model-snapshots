<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots;

use EriBloo\LaravelModelSnapshots\Contracts\Versionist as VersionistContract;

class SnapshotOptions
{
    public VersionistContract $versionist;

    /**
     * @var array<int, string>
     */
    public array $snapshotExcept = [];

    public bool $snapshotHidden = false;

    public bool $snapshotDuplicate = false;

    protected function __construct()
    {
        $this->versionist = app(VersionistContract::class);
    }

    public static function defaults(): self
    {
        return new self();
    }

    public function withVersionist(VersionistContract $versionist): static
    {
        $this->versionist = $versionist;

        return $this;
    }

    /**
     * @param  array<int, string>  $attributes
     */
    public function snapshotExcept(array $attributes): static
    {
        $this->snapshotExcept = $attributes;

        return $this;
    }

    public function snapshotHidden(bool $option = true): static
    {
        $this->snapshotHidden = $option;

        return $this;
    }

    public function snapshotDuplicate(bool $option = true): static
    {
        $this->snapshotDuplicate = $option;

        return $this;
    }
}
