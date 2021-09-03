<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Jobs\DeleteMediaLike;
use Aparlay\Core\Models\Media;
use Exception;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MediaDeleted
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     * @throws Exception
     */
    public function __construct(Media $media)
    {
        $creatorUser = $media->userObj;
        $creatorUser->media_count--;

        $file = config('app.cdn.videos').$media->file;
        $cover = config('app.cdn.covers').$media->filename.'.jpg';
        $creatorUser->removeFromSet('medias', ['_id' => $media->_id, 'file' => $file, 'cover' => $cover, 'status' => $media->status]);
        $creatorUser->count_fields_updated_at = array_merge(
            $creatorUser->count_fields_updated_at,
            ['medias' => DT::utcNow()]
        );
        $creatorUser->save();

        dispatch((new DeleteMediaLike((string) $media->_id, (string) $creatorUser->_id))->onQueue('low'));
    }
}
