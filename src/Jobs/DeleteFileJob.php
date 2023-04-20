<?php

namespace Aparlay\Core\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DeleteFileJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    private string $fileName;
    private string $fileDisk;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries         = 1;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int|array
     */
    public $backoff           = 1;

    /**
     * Create a new job instance.
     *
     * @throws Exception
     *
     * @return void
     */
    public function __construct(
        string $fileDisk,
        string $fileName,
    ) {
        $this->fileDisk = $fileDisk;
        $this->fileName = $fileName;
    }

    public function handle()
    {
        try {
            $fileExists = Storage::disk($this->fileDisk)->fileExists($this->fileName);
            if ($fileExists) {
                Storage::disk($this->fileDisk)->delete($this->fileName);
            }
        } catch (Throwable $throwable) {
            Log::error('Unable to delete file: ' . $throwable->getMessage());
        }
    }
}
