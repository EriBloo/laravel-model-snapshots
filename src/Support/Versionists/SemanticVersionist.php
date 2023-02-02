<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Support\Versionists;

use EriBloo\LaravelModelSnapshots\Contracts\VersionistInterface;

class SemanticVersionist implements VersionistInterface
{
    /**
     * @var int<0,2>
     */
    protected int $level = 1;

    /**
     * @return string
     */
    public function getFirstVersion(): string
    {
        return match ($this->level) {
            0 => '1.0.0',
            1 => '0.1.0',
            2 => '0.0.1'
        };
    }

    /**
     * @param  string  $version
     * @return string
     */
    public function getNextVersion(string $version): string
    {
        $versionParts = explode('.', $version);
        $versionParts[$this->level] = (string) ((int) $versionParts[$this->level] + 1);

        for ($i = $this->level + 1; $i <3; $i++) {
            $versionParts[$i] = '0';
        }

        return implode('.', $versionParts);
    }

    /**
     * @return $this
     */
    public function incrementMajor(): static
    {
        $this->level = 0;

        return $this;
    }

    /**
     * @return $this
     */
    public function incrementMinor(): static
    {
        $this->level = 1;

        return $this;
    }

    /**
     * @return $this
     */
    public function incrementPatch(): static
    {
        $this->level = 2;

        return $this;
    }
}
