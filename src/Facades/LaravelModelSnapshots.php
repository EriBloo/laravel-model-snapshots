<?php

namespace EriBloo\LaravelModelSnapshots\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \EriBloo\LaravelModelSnapshots\LaravelModelSnapshots
 */
class LaravelModelSnapshots extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \EriBloo\LaravelModelSnapshots\LaravelModelSnapshots::class;
    }
}
