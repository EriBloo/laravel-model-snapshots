<?php

declare(strict_types=1);

use EriBloo\LaravelModelSnapshots\Snapshotter;
use Illuminate\Database\Eloquent\Model;

if (!function_exists('snapshot')) {
    function snapshot(Model $model): Snapshotter
    {
        return new Snapshotter($model);
    }
}
