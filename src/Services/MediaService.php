<?php

namespace Aparlay\Core\Services;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Models\MediaVisit;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Psr\SimpleCache\InvalidArgumentException;

class MediaService
{
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
    public static function delete(Media $media): void
    {
        $model = Media::media($media->_id)->firstOrFail();

        if ($model !== null && $media->status !== Media::STATUS_USER_DELETED) {
            $model->update(['status' => Media::STATUS_USER_DELETED]);
        }
    }

    /**
     * @param  string  $type
     * @return Paginator
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public static function getByType(string $type)
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

        $deviceId = request()->headers->get('X-DEVICE-ID', '');
        $cacheKey = 'media_visits'.'_'.$deviceId;
        if ($type !== 'following') {
            if (! auth()->guest()) {
                $userId = auth()->user()->_id;
                $query->notVisitedByUserAndDevice($userId, $deviceId);
            } else {
                $query->notVisitedByDevice($deviceId);
            }
            $count = $query->count();
            if ($count === 0) {
                if (! auth()->guest()) {
                    MediaVisit::user(auth()->user()->_id)->get()->delete();
                }
                cache()->delete($cacheKey);
                redirect('index');
            }
        }

        $data = $query->paginate(10);
        $visited = cache()->has($cacheKey) ? cache()->get($cacheKey) : [];
        foreach ($data->items() as $model) {
            $visited[] = $model->_id;
        }
        cache()->set($cacheKey, array_unique($visited, SORT_REGULAR), config('app.cache.veryLongDuration'));

        return $data;
    }
}
