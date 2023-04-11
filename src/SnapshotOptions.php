<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots;

use Closure;
use EriBloo\LaravelModelSnapshots\Contracts\VersionistInterface;

class SnapshotOptions
{
    public VersionistInterface $versionist;

    /**
     * @var array<int, string>
     */
    public array $snapshotExcept = [];

    public bool $snapshotHidden = false;

    public bool $snapshotDuplicate = false;

    protected function __construct()
    {
        $this->versionist = app(VersionistInterface::class);
    }

    public static function defaults(): self
    {
        return new self();
    }

    /**
     * @param  VersionistInterface|Closure(VersionistInterface): VersionistInterface  $versionist
     * @return $this
     */
    public function withVersionist(VersionistInterface|Closure $versionist): static
    {
        $this->versionist = $versionist instanceof Closure ? $versionist($this->versionist) : $versionist;

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
