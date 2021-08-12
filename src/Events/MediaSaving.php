<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Aparlay\Core\Services\MediaService;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MediaSaving
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
        ModelSaving::dispatch($media);

        $media->hashtags = MediaService::extractHashtags($media->description);

        $extractedPeople = MediaService::extractPeople($media->description);
        if (! empty($extractedPeople)) {
            $users = [];
            $usersQuery = User::select(['username', 'avatar', '_id'])->usernames($extractedPeople)->limit(20)->get();
            if (! $usersQuery->isEmpty()) {
                foreach ($usersQuery->toArray() as $user) {
                    $users[] = $media->createSimpleUser($user);
                }
            }
            $media->people = $users;
        }

        if ($media->wasChanged('file') && strpos($media->file, config('app.cdn.videos')) !== false) {
            $media->file = str_replace(config('app.cdn.videos'), '', $media->file);
        }

        if ($media->status === Media::STATUS_DENIED) {
            $media->visibility = Media::VISIBILITY_PRIVATE;
        }

        if ($media->wasRecentlyCreated) {
            $media->slug = MediaService::generateSlug(6);
        }
    }
}
