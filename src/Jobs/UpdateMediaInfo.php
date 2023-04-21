<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UpdateMediaInfo implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    public User $user;
    public Media $media;
    public string $file;
    public string $user_id;
    public string $media_id;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries         = 30;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 3;

    /**
     * Create a new job instance.
     *
     * @throws Exception
     *
     * @return void
     */
    public function __construct(string $userId, string $mediaId, string $file)
    {
        if (($this->user = User::user($userId)->first()) === null) {
            throw new Exception(__CLASS__ . PHP_EOL . 'User not found with id ' . $userId);
        }

        if (($this->media = Media::media($mediaId)->first()) === null) {
            throw new Exception(__CLASS__ . PHP_EOL . 'Media not found with id ' . $userId);
        }

        $this->user_id  = $userId;
        $this->media_id = $mediaId;
        $this->file     = $file;
    }

    /**
     * Execute the job.
     *
     * @throws FileNotFoundException
     *
     * @return void
     */
    public function handle()
    {
        $b2          = Storage::disk('b2-videos');

        $fileHistory = [
            'mime_type' => '',
            'hash' => '',
            'size' => 0,
            'file' => $this->file,
            'created_at' => DT::utcNow(),
        ];
        if ($b2->fileExists($this->file)) {
            $ctx                      = hash_init('sha1');
            hash_update_stream($ctx, $b2->readStream($this->file));
            $fileHistory['hash']      = hash_final($ctx);
            $fileHistory['size']      = $b2->size($this->file);
            $fileHistory['mime_type'] = $b2->mimeType($this->file);
            $this->media->addToSet('files_history', $fileHistory);
            $this->media->save();
        } else {
            $this->user->notify(new JobFailed(self::class, $this->attempts(), 'File not exists'));
        }
    }

    public function failed(Throwable $exception): void
    {
        if (($user = User::admin()->first()) !== null) {
            $user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
        }
    }
}
