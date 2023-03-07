<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Support\Versionists;

use EriBloo\LaravelModelSnapshots\Contracts\VersionistInterface;

class IncrementingVersionist implements VersionistInterface
{
    public function getFirstVersion(): string
    {
        return '1';
    }

    public function getNextVersion(string $version): string
    {
        return (string) ((int) $version + 1);
    }
}
