<?php

namespace Aparlay\Core\Api\V1\Policies;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaComment;
use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class MediaCommentPolicy
{
    use HandlesAuthorization;

    public function view(User | Authenticatable $user, Media $media)
    {
        $userId = $user?->_id;

        $isBlocked = Block::select(['created_by', '_id'])->creator($media->created_by)->user($userId)->exists();
        if (! $isBlocked) {
            return Response::allow();
        }

        return Response::deny(__('You cannot view comments for this video at the moment.'));
    }

    /**
     * Responsible for check the user can create models.
     *
     * @param  User|Authenticatable  $user
     * @param  Media  $media
     * @return Response
     */
    public function create(User | Authenticatable $user, Media $media)
    {
        $userId = $user?->_id;

        $isBlocked = Block::select(['created_by', '_id'])->creator($media->created_by)->user($userId)->exists();

        if ($isBlocked) {
            return Response::deny(__('You cannot create comment for this video at the moment.'));
        }

        if (! $media->is_comments_enabled) {
            return Response::deny(__('Comments are disabled for this video.'));
        }

        return Response::allow();
    }

    /**
     * Responsible for check the user can delete the model.
     *
     * @param  User|Authenticatable  $user
     * @param  Media  $media
     * @return Response
     */
    public function delete(User | Authenticatable $user, MediaComment $mediaComment)
    {
        $userId = $user?->_id;

        if ((string) $mediaComment->mediaObj->created_by === (string) $userId) {
            return Response::allow();
        }

        if ($userId === null || (string) $mediaComment->creator['_id'] !== (string) $userId) {
            return Response::deny(__('You can only delete comment that you\'ve created.'));
        }

        return Response::allow();
    }
}
