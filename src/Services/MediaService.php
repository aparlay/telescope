<?php

namespace Aparlay\Core\Services;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Resources\MediaResource;
use Aparlay\Core\Models\MediaVisit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use MongoDB\BSON\ObjectId;

class MediaService
{
    /**
     * @param int $length
     * @return string
     */
    public static function generateSlug(int $length): string
    {
        $slug = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);

        return (null === Media::slug($slug)->first()) ? $slug : self::generateSlug($length);
    }

    /**
     * @param string $description
     * @return array
     * @throws \Exception
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
     * @param string $description
     * @return array
     * @throws \Exception
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
     * @param string $type
     * @return Collection|LengthAwarePaginator|AnonymousResourceCollection|array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function getByType(string $type): Collection | LengthAwarePaginator | AnonymousResourceCollection | array
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
        //$deviceId = request()->headers->get('X-DEVICE-ID', '');
        //$cacheKey = 'media_visits'.'_'.$deviceId;
        if ($type !== 'following') {
            /*
             * @todo MediaVisit
             * if (! auth()->guest()) {
                $userId = auth()->user()->_id;
                $query->notVisitedByUserAndDevice($userId, $deviceId);
            } else {
                $query->notVisitedByDevice($deviceId);
            }
            $count = $query->count();
            if ($count === 0) {
                if (! auth()->guest()) {
                    MediaVisit::user(auth()->user()->_id)->delete();
                }
                cache()->delete($cacheKey);
                redirect('index');
            }*/
            $provider = $query->paginate(15);
        } else {
            $provider = $query->get();
        }
        /*$visited = cache()->has($cacheKey) ? cache()->get($cacheKey) : [];
        foreach ($provider as $model) {
            $visited[] = $model->_id;
        }
        cache()->set($cacheKey, array_unique($visited, SORT_REGULAR), config('app.cache.veryLongDuration'));
        */
        if ($type === 'following') {
            $provider = MediaResource::collection($provider);
        }

        return $provider;
    }
}
