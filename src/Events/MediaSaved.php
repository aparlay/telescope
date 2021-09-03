<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Jobs\DeleteMediaLike;
use Aparlay\Core\Models\Media;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use MongoDB\BSON\ObjectId;

class MediaSaved
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     * @throws \Exception
     */
    public function __construct(Media $media)
    {
        $creatorUser = $media->userObj;

        if ($media->wasRecentlyCreated || $media->wasChanged('status')) {
            $creatorUserMedias = $creatorUser->medias;
            if (! empty($creatorUserMedias)) {
                foreach ($creatorUserMedias as $creatorUserMedia) {
                    if ((string) $creatorUserMedia['_id'] === (string) $media->_id) {
                        $creatorUser->removeFromSet('medias', $creatorUserMedia);
                    }
                }
            }

            $creatorUser->media_count = Media::creator($media->created_by)->count();
            $medias = [];
            $completedMedias = Media::creator($media->created_by)->completed()->recentFirst()->limit(30)->get();
            if (! $completedMedias->isEmpty()) {
                foreach ($completedMedias as $completedMedia) {
                    $basename = basename($completedMedia['file'], '.'.pathinfo($completedMedia['file'], PATHINFO_EXTENSION));
                    $file = config('app.cdn.videos').$completedMedia['file'];
                    $cover = config('app.cdn.covers').$basename.'.jpg';
                    $medias[] = ['_id' => new ObjectId($completedMedia['_id']), 'file' => $file, 'cover' => $cover, 'status' => $completedMedia['status']];
                }
            }
            $creatorUser->medias = $medias;
            $creatorUser->count_fields_updated_at = array_merge(
                $creatorUser->count_fields_updated_at,
                ['medias' => DT::utcNow()]
            );
            $creatorUser->save();
        }

        if ($media->isDirty('status') && $media->status === Media::STATUS_USER_DELETED) {
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

        if ($media->isDirty('status') && $media->status === Media::STATUS_ADMIN_DELETED) {
            dispatch((new DeleteMediaLike((string) $media->_id, (string) $creatorUser->_id))->onQueue('low'));
        }
    }
}
