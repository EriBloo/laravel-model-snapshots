<?php

declare(strict_types=1);

use EriBloo\LaravelModelSnapshots\Support\Versionists\IncrementingVersionist;

it('returns proper incrementing versionist first version', function () {
    expect((new IncrementingVersionist())->getFirstVersion())->toBe('1');
});

it('returns proper incrementing versionist next version', function () {
    expect((new IncrementingVersionist())->getNextVersion('1'))->toBe('2');
});
