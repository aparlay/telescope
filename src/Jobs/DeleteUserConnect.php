<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Models\Block;
use Aparlay\Core\Models\Follow;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class DeleteUserConnect implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 20;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public int $maxExceptions = 3;


    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int|array
     */
    public $backoff = 60;

    /**
     * Create a new job instance.
     *
     * @return void
     * @throws Exception
     */
    public function __construct(string $userId)
    {
        if (($this->user = User::user($userId)->first()) === null) {
            throw new Exception(__CLASS__.PHP_EOL.'User not found!');
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Follow::creator($this->user->_id)->chunk(200, function ($models) {
            foreach ($models as $model) {
                $model->is_deleted = true;
                $model->save();
            }
        });


        Follow::user($this->user->_id)->chunk(200, function ($models) {
            foreach ($models as $model) {
                $model->is_deleted = true;
                $model->save();
            }
        });

        Block::creator($this->user->_id)->chunk(200, function ($models) {
            foreach ($models as $model) {
                $model->is_deleted = true;
                $model->save();
            }
        });

        Block::user($this->user->_id)->chunk(200, function ($models) {
            foreach ($models as $model) {
                $model->is_deleted = true;
                $model->save();
            }
        });
    }

    public function failed(Throwable $exception)
    {
        $this->user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
    }
}
