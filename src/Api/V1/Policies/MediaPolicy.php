<?php

namespace Aparlay\Core\Api\V1\Policies;

use Aparlay\Core\Api\V1\Models\Follow;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Enums\MediaVisibility;
use Aparlay\Core\Models\Enums\UserStatus;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class MediaPolicy
{
    use HandlesAuthorization;

    /**
     * @param  User|Authenticatable|null  $user
     * @return Response
     */
    public function viewAny(User|Authenticatable|null $user): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|Authenticatable|null  $user
     * @param  Media  $media
     * @return Response|bool
     */
    public function view(User|Authenticatable|null $user, Media $media): Response|bool
    {
        $userId = $user?->_id;

        if (in_array($media->status, [MediaStatus::USER_SUSPENDED, MediaStatus::USER_DELETED])) {
            return Response::deny(__('This content is not available for you.'));
        }

        if ($media->visibility === MediaVisibility::PUBLIC->value) {
            return Response::allow();
        }

        if (! auth()->guest()) {
            $isFollowed = Follow::query()
                ->select(['created_by', '_id'])
                ->creator($userId)
                ->user($media->created_by)
                ->accepted()
                ->exists();
            if ($isFollowed) {
                return Response::allow();
            }
        }

        return Response::deny(__('This content is not available for you.'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @return Response|bool
     */
    public function create(): Response|bool
    {
        return auth()->user()->status !== UserStatus::PENDING->value
            ? Response::allow()
            : Response::deny(__('You need to complete registration first!'));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User|Authenticatable  $user
     * @param  Media  $media
     * @return Response|bool
     */
    public function update(User|Authenticatable $user, Media $media): Response|bool
    {
        $userId = $user->_id ?? null;

        return ($userId !== null && (string) $media->created_by === (string) $userId)
            ? Response::allow()
            : Response::deny(__('You can only update media that you\'ve created.'));
    }

    /**
     * @param $user
     * @param $media
     * @return Response
     */
    public function delete($user, $media): Response
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
