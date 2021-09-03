<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Microservices\ws\WsChannel;
use Aparlay\Core\Models\MediaLike;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class MediaLikeCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(MediaLike $mediaLike)
    {
        $media = $mediaLike->mediaObj;
        $user = $media->userObj;

        $media->like_count++;
        $media->addToSet(
            'likes',
            [
                '_id' => $mediaLike->creatorObj->_id,
                'username' => $mediaLike->creatorObj->username,
                'avatar' => $mediaLike->creatorObj->avatar,
            ],
            10
        );
        $media->count_fields_updated_at = array_merge(
            $media->count_fields_updated_at,
            ['likes' => DT::utcNow()]
        );
        $media->save();

        $cacheKey = $mediaLike->getCollection().':creator:'.$mediaLike->creator['_id'];
        MediaLike::cacheByUserId((string) $mediaLike->created_by);
        Redis::sAdd($cacheKey, (string) $mediaLike->media_id);

        $user->like_count++;
        $user->addToSet(
            'likes',
            [
                '_id' => $mediaLike->creatorObj->_id,
                'username' => $mediaLike->creatorObj->username,
                'avatar' => $mediaLike->creatorObj->avatar,
            ],
            10
        );
        $user->count_fields_updated_at = array_merge(
            $user->count_fields_updated_at,
            ['likes' => DT::utcNow()]
        );
        $user->save();

        WsChannel::Push($media->created_by, 'media.like', [
            'media' => $media->simple_array,
            'user' => $mediaLike->creatorObj,
            'message' => __(':username likes your video.', ['username' => $mediaLike->creatorObj->username]),
        ]);
    }
}
