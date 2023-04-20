<?php

namespace Aparlay\Core\Commands;

use Flow\FileOpenException;
use Flow\Uploader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupCommand extends Command
{
    public $signature   = 'core:cleanup';
    public $description = 'Aparlay Ws Client';

    public function handle()
    {
        try {
            Uploader::pruneChunks(Storage::disk('local')->path('chunk'));
        } catch (FileOpenException $e) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
