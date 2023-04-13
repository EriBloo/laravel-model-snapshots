<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Support\Versionists;

use EriBloo\LaravelModelSnapshots\Contracts\Versionist as VersionistContract;

class SemanticVersionist implements VersionistContract
{
    /**
     * @var int<0, 2>
     */
    protected int $level = 1;

    public function getFirstVersion(): string
    {
        return match ($this->level) {
            0 => '1.0.0',
            1 => '0.1.0',
            2 => '0.0.1'
        };
    }

    public function getNextVersion(string $version): string
    {
        $versionParts = explode('.', $version);
        $versionParts[$this->level] = (string) ((int) $versionParts[$this->level] + 1);

        for ($i = $this->level + 1; $i < 3; $i++) {
            $versionParts[$i] = '0';
        }

        return implode('.', $versionParts);
    }

    public function incrementMajor(): static
    {
        $this->level = 0;

        return $this;
    }

    public function incrementMinor(): static
    {
        $this->level = 1;

        return $this;
    }

    public function incrementPatch(): static
    {
        $this->level = 2;

        return $this;
    }
}
