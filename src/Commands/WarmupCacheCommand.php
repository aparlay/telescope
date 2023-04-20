<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Jobs\WarmupSimpleUserCacheJob;
use Aparlay\Core\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;

class WarmupCacheCommand extends Command implements Isolatable
{
    public $signature   = 'core:warmup';
    public $description = 'This command is responsible for warm up cache';

    public function handle()
    {
        $this->info('Warming up simple user cache');
        WarmupSimpleUserCacheJob::dispatch();
        Media::CachePublicExplicitMediaIds();
        Media::CachePublicToplessMediaIds();
        Media::CachePublicMediaIds();

        return self::SUCCESS;
    }
}
