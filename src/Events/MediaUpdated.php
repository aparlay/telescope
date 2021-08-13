<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Jobs\DeleteMediaLike;
use Aparlay\Core\Models\Media;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class MediaUpdated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Media $media)
    {
        $creatorUser = $media->userObj;

        if ($media->status === Media::STATUS_COMPLETED || $media->status === Media::STATUS_CONFIRMED) {
            Cache::forget('Media.Index.TotalCount.Public');
        }

        if ($media->status === Media::STATUS_USER_DELETED) {
            Cache::forget('Media.Index.TotalCount.Public');
            $creatorUser->refresh();

            $creatorUser->media_count--;

            $file = config('app.cdn.videos').$media->file;
            $cover = config('app.cdn.covers').$media->filename.'.jpg';
            $creatorUser->removeFromSet('medias', ['_id' => $media->_id, 'file' => $file, 'cover' => $cover, 'status' => $media->status]);
            $creatorUser->count_fields_updated_at = array_merge(
                $creatorUser->count_fields_updated_at,
                ['medias' => DT::utcNow()]
            );
            $creatorUser->save();

            dispatch((new DeleteMediaLike($media->id, $creatorUser->_id))->onQueue('low'));
        }
    }
}
