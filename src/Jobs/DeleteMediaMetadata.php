<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Throwable;

class DeleteMediaMetadata implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 1;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     * @throws Exception
     */
    public function __construct(public string $file, public string $disk = 'upload', public array $context = [])
    {
        $this->onQueue(config('app.server_specific_queue'));
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $storage = Storage::disk($this->disk);
        if (! $storage->exists($this->file)) {
            Log::error('File not found: '.$storage->path($this->file));

            return;
        }

        $process = new Process(['exiftool', '-all=', '-overwrite_original', $storage->path($this->file)]);
        $process->run();

        if (! $process->isSuccessful()) {
            $messageInfo = array_map(fn ($key, $value) => "$key: $value", array_keys($this->context), array_values($this->context));
            Log::error('Metadata removal failed for file '.$this->file."\nContext:\n".implode("\n", $messageInfo));
            Log::error($process->getErrorOutput());
        }
    }

    public function failed(Throwable $exception): void
    {
        if (($user = User::admin()->first()) !== null) {
            $user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
        }
    }
}
