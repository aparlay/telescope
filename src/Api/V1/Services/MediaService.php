<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Models\Alert;
use Aparlay\Core\Api\V1\Models\Follow;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaVisit;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Repositories\MediaRepository;
use Aparlay\Core\Api\V1\Requests\MediaRequest;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

class MediaService
{
    protected MediaRepository $mediaRepository;

    public function __construct()
    {
        $this->mediaRepository = new MediaRepository(new Media());
    }

    /**
     * @param  MediaRequest  $request
     * @return Media
     */
    public function create(MediaRequest $request): Media
    {
        return $this->mediaRepository->store($request);
    }

    /**
     * @param  int  $length
     * @return string
     */
    public static function generateSlug(int $length): string
    {
        $slug = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);

        return (null === Media::slug($slug)->first()) ? $slug : self::generateSlug($length);
    }

    /**
     * @param  string  $description
     * @return array
     * @throws Exception
     */
    public static function extractPeople(string $description): array
    {
        $description = trim($description);
        $people = [];
        foreach (explode(' ', $description) as $item) {
            if (isset($item[0]) && $item[0] === '@' && substr_count($item, '@') === 1) {
                $people[] = substr($item, 1);
            }
        }

        return array_slice($people, 0, 20);
    }

    /**
     * @param  string  $description
     * @return array
     * @throws Exception
     */
    public static function extractHashtags(string $description): array
    {
        $description = trim($description);
        $tags = [];
        foreach (explode(' ', $description) as $item) {
            if (isset($item[0]) && $item[0] === '#' && substr_count($item, '#') === 1) {
                $tags[] = substr($item, 1);
            }
        }

        return array_slice($tags, 0, 20);
    }

    /**
     * @param  Media  $media
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(Media $media): void
    {
        $model = Media::media($media->_id)->firstOrFail();

        if ($model !== null && $model->status !== Media::STATUS_USER_DELETED) {
            $this->mediaRepository->update(['status' => Media::STATUS_USER_DELETED], $model->_id);
        }
    }

    /**
     * @param  string  $type
     * @return LengthAwarePaginator
     * @throws InvalidArgumentException|Exception
     */
    public function getByType(string $type)
    {
        $query = Media::query();

        if (! auth()->guest() && $type === 'following') {
            $query->availableForFollower()->following(auth()->user()->_id)->recentFirst();
        } else {
            $query->public()->confirmed()->sort();
        }

        if (! auth()->guest()) {
            $query->notBlockedFor(auth()->user()->_id);
        }

        $deviceId = request()->header('X-DEVICE-ID', '');
        $cacheKey = (new MediaVisit())->getCollection().':'.$deviceId;

        if ($type !== 'following') {
            $originalQuery = $query;
            $originalData = $originalQuery->paginate(5, ['*'], 'page', 1)->withQueryString();

            if (! auth()->guest()) {
                $userId = auth()->user()->_id;
                $query->notVisitedByUserAndDevice($userId, $deviceId);
            } else {
                $query->notVisitedByDevice($deviceId);
            }

            $data = $query->paginate(5)->withQueryString();

            if ($data->isEmpty() || $data->total() <= 5) {
                if (! auth()->guest()) {
                    MediaVisit::user(auth()->user()->_id)->delete();
                }
                Cache::store('redis')->delete($cacheKey);

                if ($data->isEmpty()) {
                    $data = $originalData;
                }
            }
        } else {
            $data = $query->paginate(5)->withQueryString();
        }

        $visited = Cache::store('redis')->get($cacheKey, []);
        foreach ($data->items() as $model) {
            $visited[] = $model->_id;
        }
        Cache::store('redis')->set($cacheKey, array_unique($visited, SORT_REGULAR), config('app.cache.veryLongDuration'));

        return $data;
    }

    /**
     * @param  User  $user
     * @return LengthAwarePaginator
     * @throws InvalidArgumentException
     */
    public function getByUser(User $user)
    {
        $userId = $user->_id;
        $query = Media::creator($userId)->recentFirst();

        if (auth()->guest()) {
            $query->confirmed()->public();
        } elseif ((string) $userId === (string) auth()->user()->_id) {
            $query->with(['alertObjs' => function ($query) {
                $query->where('status', Alert::STATUS_NOT_VISITED);
            }])->availableForOwner();
        } else {
            $isFollowed = Follow::select(['user._id', '_id'])
                ->creator(auth()->user()->_id)
                ->user($userId)
                ->accepted()
                ->exists();
            if (empty($isFollowed)) {
                $query->confirmed()->public();
            } else {
                $query->availableForFollower();
            }
        }

        return $query->paginate(15);
    }
}
