<?php

namespace Aparlay\Core\Commands;

use Flow\FileOpenException;
use Flow\Uploader;
use Illuminate\Console\Command;

class CleanupCommand extends Command
{
    public $signature = 'core:ws';

    public $description = 'Aparlay Ws Client';

    public function handle()
    {
        try {
            Uploader::pruneChunks(config('app.avatar.upload_directory'));
        } catch (FileOpenException $e) {
            return ExitCode::TEMPFAIL;
        }
    }
}
