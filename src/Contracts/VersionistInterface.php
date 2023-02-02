<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Contracts;

interface VersionistInterface
{
    /**
     * Get first version id.
     *
     * @return string
     */
    public function getFirstVersion(): string;

    /**
     * Get next version id.
     *
     * @param  string  $version current version id
     * @return string
     */
    public function getNextVersion(string $version): string;
}
