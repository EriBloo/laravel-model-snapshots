<?php

declare(strict_types=1);

use EriBloo\LaravelModelSnapshots\Support\Versionists\IncrementingVersionist;
use EriBloo\LaravelModelSnapshots\Support\Versionists\SemanticVersionist;

it('returns proper incrementing versionist first version', function () {
    expect((new IncrementingVersionist())->getFirstVersion())->toBe('1');
});

it('returns proper incrementing versionist next version', function () {
    expect((new IncrementingVersionist())->getNextVersion('1'))->toBe('2');
});

it('returns proper semantic versionist first version', function () {
    expect((new SemanticVersionist())->getFirstVersion())->toBe('0.1.0')
        ->and((new SemanticVersionist())->incrementMajor()->getFirstVersion())->toBe('1.0.0')
        ->and((new SemanticVersionist())->incrementPatch()->getFirstVersion())->toBe('0.0.1');
});

it('returns proper semantic versionist next version', function () {
    expect((new SemanticVersionist())->getNextVersion('0.1.0'))->toBe('0.2.0')
        ->and((new SemanticVersionist())->incrementMajor()->getNextVersion('0.1.0'))->toBe('1.0.0')
        ->and((new SemanticVersionist())->incrementPatch()->getNextVersion('0.1.0'))->toBe('0.1.1')
        ->and((new SemanticVersionist())->getNextVersion('1.0.0'))->toBe('1.1.0')
        ->and((new SemanticVersionist())->incrementMajor()->getNextVersion('1.0.0'))->toBe('2.0.0')
        ->and((new SemanticVersionist())->incrementPatch()->getNextVersion('1.0.0'))->toBe('1.0.1')
        ->and((new SemanticVersionist())->getNextVersion('0.0.1'))->toBe('0.1.0')
        ->and((new SemanticVersionist())->incrementMajor()->getNextVersion('0.0.1'))->toBe('1.0.0')
        ->and((new SemanticVersionist())->incrementPatch()->getNextVersion('0.0.1'))->toBe('0.0.2');
});
