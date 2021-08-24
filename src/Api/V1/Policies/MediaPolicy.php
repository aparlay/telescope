<?php

namespace Aparlay\Core\Api\V1\Policies;

use Aparlay\Core\Api\V1\Models\Follow;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class MediaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \Aparlay\Core\Api\V1\Models\User  $user
     * @param  \Aparlay\Core\Api\V1\Models\Media  $media
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User | null $user, Media $media)
    {
        $userId = $user?->_id;

        if ($media->visibility === Media::VISIBILITY_PUBLIC) {
            return Response::allow();
        }

        $isFollowed = Follow::select(['created_by', '_id'])
            ->creator($userId)
            ->user($media->created_by)
            ->accepted()
            ->exists();
        if ($isFollowed) {
            return Response::allow();
        }

        /*
        if ($media->visibility === Media::VISIBILITY_PRIVATE && $userId === null) {
            return Response::deny(__('You can only view media that you\'ve created.'));;
        }
        */

        return Response::deny(__('You can only view media that you\'ve created.'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \Aparlay\Core\Api\V1\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create()
    {
        return auth()->user()->status !== User::STATUS_PENDING
            ? Response::allow()
            : Response::deny(__('You need to complete registration first!'));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \Aparlay\Core\Api\V1\Models\User  $user
     * @param  \Aparlay\Core\Api\V1\Models\Media  $media
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Media $media)
    {
        $userId = $user->_id ?? null;

        return ($userId === null || (string) $media->created_by !== (string) $userId)
            ? Response::allow()
            : Response::deny(__('You can only update media that you\'ve created.'));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \Aparlay\Core\Api\V1\Models\User  $user
     * @param  \Aparlay\Core\Api\V1\Models\Media  $media
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Media $media)
    {
        $userId = $user->_id ?? null;

        if ($userId === null || (string) $media->created_by !== (string) $userId) {
            return Response::deny(__('You can only delete media that you\'ve created.'));
        }

        if ($media->is_protected) {
            return Response::deny(__('Video is protected and you cannot delete it.'));
        }

        return Response::allow();
    }
}
