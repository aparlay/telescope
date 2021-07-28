<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Models\Block;
use Aparlay\Core\Models\Follow;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateAvatar implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $avatar;
    public $user_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($avatar, $user_id)
    {
        $this->avatar = $avatar;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (($user = User::user(['_id' => $this->user_id])->first()) === null) {
            throw new PermanentException(__CLASS__ . PHP_EOL . 'User not found!');
        }

        foreach (Media::creator($this->user_id)->chunk() as $media) {
            $creator = $media->creator;
            $creator['avatar'] = $user->avatar;
            $media->creator = $creator;
            $media->save(true, ['creator']);
        }

        foreach (Follow::creator($this->user_id)->each() as $follow) {
            $creator = $follow->creator;
            $creator['avatar'] = $user->avatar;
            $follow->creator = $creator;
            $follow->save(true, ['creator']);
        }

        foreach (Follow::user($this->user_id)->each() as $follow) {
            $userArray = $follow->user;
            $userArray['avatar'] = $user->avatar;
            $follow->user = $userArray;
            $follow->save(true, ['user']);
        }

        foreach (Block::creator($this->user_id)->each() as $block) {
            $creator = $block->creator;
            $creator['avatar'] = $user->avatar;
            $block->creator = $creator;
            $block->save(true, ['creator']);
        }

        foreach (Block::user($this->user_id)->each() as $block) {
            $userArray = $block->user;
            $userArray['avatar'] = $user->avatar;
            $block->user = $userArray;
            $block->save(true, ['creator']);
        }
    }
}
