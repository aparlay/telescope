<?php

namespace Aparlay\Core\Api\V1\Policies;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Http\Response;

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
    public function view(User $user, Media $media)
    {
        return $media->isVisibleBy($user->_id)
            ? Response::allow()
            : Response::deny('You can only view media that you\'ve created.');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \Aparlay\Core\Api\V1\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return auth()->user()->status !== User::STATUS_PENDING
            ? Response::allow()
            : Response::deny('You need to complete registration first!');
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
            : Response::deny('You can only update media that you\'ve created.');
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
            return Response::deny('You can only delete media that you\'ve created.');
        }

        if ($media->is_protected) {
            return Response::deny('Video is protected and you cannot delete it.');
        }

        return Response::allow();
    }
}
