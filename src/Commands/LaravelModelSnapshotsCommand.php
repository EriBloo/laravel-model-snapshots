<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Commands;

use Illuminate\Console\Command;

class LaravelModelSnapshotsCommand extends Command
{
    public $signature = 'laravel-model-snapshots';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
