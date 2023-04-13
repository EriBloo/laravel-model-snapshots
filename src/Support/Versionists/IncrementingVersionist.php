<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Support\Versionists;

use EriBloo\LaravelModelSnapshots\Contracts\Versionist as VersionistContract;

class IncrementingVersionist implements VersionistContract
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
