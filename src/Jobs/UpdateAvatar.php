<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Models\Block;
use Aparlay\Core\Models\Follow;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class UpdateAvatar implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $avatar;
    public User $user;


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
    public function __construct(string $avatar, string $userId)
    {
        $this->avatar = $avatar;
        if (($this->user = User::user($userId)->first()) === null) {
            throw new Exception(__CLASS__ . PHP_EOL . 'User not found!');
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Media::creator($this->user->_id)->chunk(200, function ($models) {
            foreach ($models as $media) {
                $creator = $media->creator;
                $creator['avatar'] = $this->user->avatar;
                $media->creator = $creator;
                $media->save();
            }
        });

        Follow::creator($this->user->_id)->chunk(200, function ($models) {
            foreach ($models as $follow) {
                $creator = $follow->creator;
                $creator['avatar'] = $this->user->avatar;
                $follow->creator = $creator;
                $follow->save();
            }
        });

        Follow::user($this->user->_id)->chunk(200, function ($models) {
            foreach ($models as $follow) {
                $userArray = $follow->user;
                $userArray['avatar'] = $this->user->avatar;
                $follow->user = $userArray;
                $follow->save();
            }
        });

        Block::creator($this->user->_id)->chunk(200, function ($models) {
            foreach ($models as $block) {
                $creator = $block->creator;
                $creator['avatar'] = $this->user->avatar;
                $block->creator = $creator;
                $block->save();
            }
        });

        Block::user($this->user->_id)->chunk(200, function ($models) {
            foreach ($models as $block) {
                $userArray = $block->user;
                $userArray['avatar'] = $this->user->avatar;
                $block->user = $userArray;
                $block->save();
            }
        });
    }

    public function failed(Throwable $exception)
    {
        $this->user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
    }
}
