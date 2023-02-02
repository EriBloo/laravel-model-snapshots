<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Support\Versionists;

use EriBloo\LaravelModelSnapshots\Contracts\VersionistInterface;

class IncrementingVersionist implements VersionistInterface
{
    /**
     * @return string
     */
    public function getFirstVersion(): string
    {
        return '1';
    }

    /**
     * @param  string  $version
     * @return string
     */
    public function getNextVersion(string $version): string
    {
        return (string) ((int) $version + 1);
    }
}
