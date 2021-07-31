<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\FileExistsException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UploadAvatar implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;
    public string $file;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 10;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public int $maxExceptions = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     * @throws Exception
     */
    public function __construct(string $userId, string $file)
    {
        $this->file = $file;
        if (($this->user = User::user($userId)->first()) === null) {
            throw new Exception(__CLASS__ . PHP_EOL . 'User not found!');
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $resource = Storage::disk('upload')->readStream($this->file);
        Storage::disk('b2-avatars')->writeStream($this->file, $resource);
        Storage::disk('gc-avatars')->writeStream($this->file, $resource);


        $this->user->avatar = Cdn::avatar($this->file);
        $this->user->save(false, ['avatar']);
    }

    public function failed(Throwable $exception)
    {
        $this->user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
    }
}
