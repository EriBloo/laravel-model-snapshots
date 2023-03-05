<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots;

use Closure;
use EriBloo\LaravelModelSnapshots\Contracts\VersionistInterface;

class SnapshotOptions
{
    /**
     * @var VersionistInterface
     */
    public VersionistInterface $versionist;

    /**
     * @var array<int, string>
     */
    public array $snapshotExcept = [];

    /**
     * @var bool
     */
    public bool $snapshotHidden = false;

    /**
     *
     */
    protected function __construct()
    {
        $this->versionist = app(VersionistInterface::class);
    }

    /**
     * @return self
     */
    public static function defaults(): self
    {
        return new self();
    }

    /**
     * @param VersionistInterface|Closure $versionist
     * @return $this
     */
    public function withVersionist(VersionistInterface|Closure $versionist): static
    {
        $this->versionist = $versionist instanceof Closure ? $versionist($this->versionist) : $versionist;

        return $this;
    }

    /**
     * @param array<int, string> $attributes
     * @return $this
     */
    public function snapshotExcept(array $attributes): static
    {
        $this->snapshotExcept = $attributes;

        return $this;
    }

    /**
     * @param bool $option
     * @return $this
     */
    public function snapshotHidden(bool $option = true): static
    {
        $this->snapshotHidden = $option;

        return $this;
    }
}
