<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Support\Versionists;

use EriBloo\LaravelModelSnapshots\Contracts\Versionist as VersionistContract;

class SemanticVersionist implements VersionistContract
{
    protected bool $major = false;

    public function getFirstVersion(): string
    {
        return $this->major ? '1.0' : '0.1';
    }

    public function getNextVersion(string $version): string
    {
        [$major, $minor] = explode('.', $version);

        return $this->major
            ? ((int) $major + 1) . '.' . '0'
            : $major . '.' . ((int) $minor + 1);
    }

    public function incrementMajor(): static
    {
        $this->major = true;

        return $this;
    }

    public function incrementMinor(): static
    {
        $this->major = false;

        return $this;
    }
}
