<?php

namespace Aparlay\Core\Api\V1\Policies;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class MediaLikePolicy
{
    use HandlesAuthorization;

    public function view()
    {
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

        $isBlocked = Block::select(['created_by', '_id'])
            ->creator($media->created_by)
            ->user($userId)
            ->exists();
        if ($isBlocked) {
            return Response::deny(__('You cannot like this video at the moment.'));
        }

        return Response::allow();
    }

    public function update()
    {
    }

    /**
     * Responsible for check the user can delete the model.
     *
     * @param  User|Authenticatable  $user
     * @param  Media  $media
     * @return Response
     */
    public function delete(User | Authenticatable $user, Media $media)
    {
        $userId = $user?->_id;

        $isBlocked = Block::select(['created_by', '_id'])
            ->creator($media->created_by)
            ->user($userId)
            ->exists();
        if ($isBlocked) {
            return Response::deny(__('You cannot unlike this video at the moment.'));
        }

        return Response::allow();
    }
}
