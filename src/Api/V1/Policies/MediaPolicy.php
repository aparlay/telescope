<?php

namespace Aparlay\Core\Api\V1\Policies;

use Aparlay\Core\Api\V1\Models\Follow;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Models\Enums\MediaVisibility;
use Aparlay\Core\Models\Enums\UserStatus;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class MediaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User | Authenticatable | null $user)
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \Aparlay\Core\Api\V1\Models\User  $user
     * @param  \Aparlay\Core\Api\V1\Models\Media  $media
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User | Authenticatable | null $user, Media $media)
    {
        $userId = $user?->_id;

        if ($media->visibility === MediaVisibility::PUBLIC->value) {
            return Response::allow();
        }

        if (! auth()->guest()) {
            $isFollowed = Follow::select(['created_by', '_id'])
                ->creator($userId)
                ->user($media->created_by)
                ->accepted()
                ->exists();
            if ($isFollowed) {
                return Response::allow();
            }
        }

        return Response::deny(__('You can only view media that you\'ve created.'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create()
    {
        return auth()->user()->status !== UserStatus::PENDING->value
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
    public function update(User | Authenticatable $user, Media $media)
    {
        $userId = $user->_id ?? null;

        return ($userId !== null && (string) $media->created_by === (string) $userId)
            ? Response::allow()
            : Response::deny(__('You can only update media that you\'ve created.'));
    }

    public function delete($user, $media)
    {
        $userId = $user->_id ?? null;

        if ($userId === null || (string) $media->creator['_id'] !== (string) $userId) {
            return Response::deny(__('You can only delete media that you\'ve created.'));
        }

        if ($media->is_protected) {
            return Response::deny(__('Video is protected and you cannot delete it.'));
        }

        return Response::allow();
    }
}
