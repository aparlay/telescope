<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Dto\MediaDTO;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaVisit;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Repositories\MediaRepository;
use Aparlay\Core\Api\V1\Requests\MediaRequest;
use Aparlay\Core\Api\V1\Requests\PublicFeedRequest;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Jobs\MediaBatchWatched;
use Aparlay\Core\Models\Enums\AlertStatus;
use Aparlay\Core\Models\Enums\MediaContentGender;
use Aparlay\Core\Models\Enums\MediaSortCategories;
use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Enums\UserSettingShowAdultContent;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use MongoDB\BSON\ObjectId;
use Psr\SimpleCache\InvalidArgumentException as InvalidArgumentExceptionAlias;
use Ramsey\Uuid\Uuid;
use RedisException;
use Str;

class MediaService
{
    use HasUserTrait;

    protected MediaRepository $mediaRepository;

    public function __construct()
    {
        $this->mediaRepository = new MediaRepository(new Media());
    }

    /**
     * @param  MediaRequest  $request
     *
     * @return Media
     */
    public function create(MediaRequest $request): Media
    {
        return $this->mediaRepository->store($request);
    }

    /**
     * @param  Media     $media
     * @param  MediaDTO  $mediaDto
     *
     * @return Media
     */
    public function update(Media $media, MediaDTO $mediaDto): Media
    {
        if (! empty($mediaDto->is_comments_enabled)) {
            $media->is_comments_enabled = $mediaDto->is_comments_enabled;
        }

        $media->save();

        return $media;
    }

    /**
     * @param  int  $length
     *
     * @return string
     */
    public static function generateSlug(int $length): string
    {
        $slug = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);

        return (null === Media::slug($slug)->first()) ? $slug : self::generateSlug($length);
    }

    /**
     * @param  string  $description
     *
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
     *
     * @return array
     * @throws Exception
     */
    public static function extractHashtags(string $description): array
    {
        $description = trim($description);
        $tags = [];
        foreach (explode(' ', $description) as $item) {
            if (isset($item[0]) && $item[0] === '#' && substr_count($item, '#') === 1) {
                $tags[] = Str::lower(substr($item, 1));
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

        if ($model !== null && $model->status !== MediaStatus::USER_DELETED->value) {
            $this->mediaRepository->update(['status' => MediaStatus::USER_DELETED->value], $model->_id);
        }
    }

    /**
     * @param  PublicFeedRequest  $request
     * @param  string             $type
     *
     * @return LengthAwarePaginator
     * @throws Exception
     */
    public function getFeedByType(PublicFeedRequest $request, string $type): LengthAwarePaginator
    {
        return match ($type) {
            'following' => $this->getFollowingFeed($request),
            'new' => $this->getNewFeed($request),
            default => $this->getFollowingFeed($request),
        };
    }

    /**
     * @param  PublicFeedRequest  $request
     *
     * @return LengthAwarePaginator
     */
    public function getFollowingFeed(PublicFeedRequest $request): LengthAwarePaginator
    {
        $query = Media::query();
        $userId = auth()->guest() ? null : auth()->user()->_id;

        if ($userId === null) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 5, 0);
        }

        $data = $query
            ->availableForFollower()
            ->following(auth()->user()->_id)
            ->recentFirst()
            ->paginate(5)
            ->withQueryString();

        $visited = [];
        foreach ($data->items() as $model) {
            $visited[] = $model->_id;
        }
        $this->incrementMediaVisitCounter($visited);

        return $data;
    }

    /**
     * @param  PublicFeedRequest  $request
     *
     * @return LengthAwarePaginator
     */
    public function getNewFeed(PublicFeedRequest $request): LengthAwarePaginator
    {
        $query = Media::query();

        $data = $query->public()
            ->confirmed()
            ->gen()
            ->recentFirst()
            ->paginate(5)
            ->withQueryString();
        $visited = [];
        foreach ($data->items() as $model) {
            $visited[] = $model->_id;
        }
        $this->incrementMediaVisitCounter($visited);

        return $data;
    }

    /**
     * @param  User  $user
     *
     * @return LengthAwarePaginator
     * @throws InvalidArgumentExceptionAlias
     */
    public function getByUser(User $user): LengthAwarePaginator
    {
        $userId = $user->_id;
        $query = Media::creator($userId)->recentFirst();
        if (! auth()->guest() && (string) $userId === (string) auth()->user()->_id) {
            $query->with([
                'alertObjs' => function ($query) {
                    $query->where('status', AlertStatus::NOT_VISITED->value);
                },
            ])->availableForOwner();
        } else {
            $query->availableForFollower();
        }

        $data = $query->paginate(15);
        $visited = [];
        foreach ($data->items() as $model) {
            $visited[] = $model->_id;
        }
        $this->incrementMediaVisitCounter($visited);

        return $data;
    }

    /**
     * @param  Media          $media
     * @param  int|float      $duration
     * @param  ObjectId|null  $userId
     * @param  string|null    $uuid
     *
     * @return void
     * @throws Exception
     */
    public function watched(
        $media,
        int|float $duration = 60,
        ObjectId|null $userId = null,
        string|null $uuid = null
    ): void {
        if ($userId !== null) {
            $this->userWatched($userId, $media, $duration);
        } else {
            $this->anonymousWatched($media, $duration);
        }

        if (! empty($uuid)) {
            $cacheKey = (new MediaVisit())->getCollection().':visited:uuid:'.$uuid;
            Redis::sadd($cacheKey, (string) $media->_id);
        }
    }

    /**
     * @param  Media      $media
     * @param  int|float  $duration
     *
     * @return void
     */
    public function anonymousWatched($media, int|float $duration = 60): void
    {
        if ($duration > 1) {
            $length = ($duration > ($media->length * 3)) ? $media->length : $duration;
            $multiplier = config('app.media.visit_multiplier', 1);
            $media->length_watched += ($length * $multiplier);
            $media->watched_count += $multiplier;
            $media->count_fields_updated_at = array_merge(
                $media->count_fields_updated_at,
                ['watch' => DT::utcNow()]
            );
            $media->save();

            $durationCacheKey = 'tracking:media:duration:'.date('Y:m:d');
            $watchedCacheKey = 'tracking:media:watched:'.date('Y:m:d');
            Redis::incrbyfloat($durationCacheKey, $length);
            Redis::incr($watchedCacheKey);
        }
    }

    /**
     * @param  ObjectId   $userId
     * @param  Media      $media
     * @param  int|float  $duration
     *
     * @return void
     * @throws Exception
     */
    public function userWatched(ObjectId $userId, $media, int|float $duration = 60): void
    {
        if (($mediaVisit = MediaVisit::query()->user($userId)->dateString(date('Y-m-d'))->first()) === null) {
            $mediaVisit = new MediaVisit();
            $mediaVisit->date = date('Y-m-d');
            $mediaVisit->user_id = $userId;
        }

        $mediaVisit->media_id = new ObjectId($media->_id);
        $mediaVisit->duration = $duration;

        if ($duration > 1) {
            $media->updateVisits($duration);
        }

        if (! $mediaVisit->save()) {
            throw new Exception('Cannot save media visit data.');
        }
    }

    /**
     * @param  PublicFeedRequest  $request
     * @param  bool               $isGuest
     * @param  bool               $isFirstPage
     *
     * @return LengthAwarePaginator
     * @throws InvalidArgumentExceptionAlias
     * @throws RedisException
     */
    public function getPublicFeeds(
        PublicFeedRequest $request,
        bool $isGuest = true,
        bool $isFirstPage = false,
    ): LengthAwarePaginator {
        $sortCategory = MediaSortCategories::REGISTERED->value;

        if ($isGuest) {
            try {
                $sortCategory = MediaSortCategories::RETURNED->value;
                if (Uuid::fromString($request->uuid)->getDateTime()->getTimestamp() > (time() - 86400)) {
                    $sortCategory = MediaSortCategories::GUEST->value;
                }
            } catch(Exception $e) {
            }
        }

        $query = Media::public()->confirmed()->genderContent($request->filter_content_gender)->sort($sortCategory);

        switch ($request->show_adult_content) {
            case UserSettingShowAdultContent::NEVER->value:
                $query->withoutTopless();
                break;
            case UserSettingShowAdultContent::TOPLESS->value:
                $query->withoutExplicit();
                break;
        }

        $originalQuery = $query;
        $originalData = $originalQuery
            ->paginate(5, ['*'], 'page', 1)
            ->withQueryString();

        if (! $isGuest && $isFirstPage) {
            $this->loadUserVisitedVideos((string) auth()->user()->_id, $request->uuid);
        }

        $mediaIds = $this->topNotVisitedVideoIds(
            $request->uuid,
            $request->show_adult_content,
            $sortCategory,
            $request->filter_content_gender
        );
        $data = $query->medias($mediaIds)->paginate(5)->withQueryString();

        if ($data->isEmpty() || $data->total() <= 5) {
            $this->flushVisitedVideos($request->uuid);

            if ($data->isEmpty()) {
                $data = $originalData;
            }
        }
        $visited = [];
        foreach ($data->items() as $model) {
            $visited[] = $model->_id;
        }

        $this->cacheVisitedVideoByUuid($visited, $request->uuid);
        $this->incrementMediaVisitCounter($visited);

        return $data;
    }

    /**
     * @param array $userId
     * @return void
     */
    public function incrementMediaVisitCounter(array $mediaIds): void
    {
        $mediaIds = array_map('strval', $mediaIds);
        dispatch(function () use ($mediaIds) {
            Media::medias($mediaIds)->increment('visit_count');
        })->afterResponse();
    }

    /**
     * @param  string  $uuid
     *
     * @return void
     * @throws InvalidArgumentExceptionAlias
     */
    public function flushVisitedVideos(string $uuid): void
    {
        if (! auth()->guest()) {
            MediaVisit::query()->user(auth()->user()->_id)->delete();
        }
        $cacheKey = (new MediaVisit())->getCollection().':visited:uuid:'.$uuid;
        Redis::unlink($cacheKey);
    }

    /**
     * @param  array   $mediaIds
     * @param  string  $uuid
     *
     * @return void
     * @throws RedisException
     */
    public function cacheVisitedVideoByUuid(array $mediaIds, string $uuid): void
    {
        if (empty($mediaIds)) {
            return;
        }

        $scoredMediaIds = [];
        foreach ($mediaIds as $key => $mediaId) {
            $scoredMediaIds[] = 0;
            $scoredMediaIds[] = $mediaId;
        }

        if (! empty($scoredMediaIds)) {
            $cacheKey = (new MediaVisit())->getCollection().':visited:uuid:'.$uuid;
            Redis::zAdd($cacheKey, ...$scoredMediaIds);
            Redis::expireat($cacheKey, now()->addDays(4)->getTimestamp());
        }
    }

    /**
     * @param  string  $userId
     * @param  string  $uuid
     *
     * @return void
     * @throws RedisException
     */
    public function loadUserVisitedVideos(string $userId, string $uuid): void
    {
        // blocked video considered as visited
        $blockedMediaIds = Media::public()
            ->confirmed()
            ->blockedFor($userId)
            ->select('_id')
            ->get()
            ->pluck('_id');
        $mediaIds = MediaVisit::query()
            ->select('media_ids')
            ->user($userId)
            ->get()
            ->pluck('media_ids')
            ->merge($blockedMediaIds)
            ->flatten()
            ->map(function ($item, $key) {
                return (string) $item;
            })
            ->toArray();

        $scoredMediaIds = [];
        foreach ($mediaIds as $key => $mediaId) {
            $scoredMediaIds[] = 0;
            $scoredMediaIds[] = $mediaId;
        }

        if (! empty($scoredMediaIds)) {
            $cacheKey = (new MediaVisit())->getCollection().':visited:uuid:'.$uuid;
            Redis::zAdd($cacheKey, ...$scoredMediaIds);
            Redis::expireat($cacheKey, now()->addDays(4)->getTimestamp());
        }
    }

    /**
     * @param  string  $uuid
     * @param  int     $explicitVisibility
     * @param  string  $sortCategory
     * @param  array   $contentGender
     *
     * @return array
     */
    public function topNotVisitedVideoIds(string $uuid, int $explicitVisibility, string $sortCategory, array $contentGender): array
    {
        $notVisitedTopVideosCacheKey = 'public_feed:uuid:'.crc32($uuid.':'.$sortCategory.':'.$explicitVisibility.':'.implode('', $contentGender));
        if (Redis::exists($notVisitedTopVideosCacheKey) < 1) {
            // cache not exists
            [
                $explicitMediaIdsCacheKey,
                $toplessMediaIdsCacheKey,
                $mediaIdsCacheKey,
                $femaleMediaIdsCacheKey,
                $maleMediaIdsCacheKey,
                $transgenderMediaIdsCacheKey,
                $visitedVideoCacheKey
            ] = $this->generateHashKeys($sortCategory, $uuid);

            // adding explicitness filter to filter bucket
            $filterBuckets[] = match ($explicitVisibility) {
                UserSettingShowAdultContent::NEVER->value => [$toplessMediaIdsCacheKey, $visitedVideoCacheKey],
                UserSettingShowAdultContent::TOPLESS->value => [$explicitMediaIdsCacheKey, $visitedVideoCacheKey],
                default => [$visitedVideoCacheKey]
            };

            // adding content filter to filter bucket
            foreach (array_diff(MediaContentGender::getAllValues(), $contentGender) as $gender) {
                $filterBuckets[] = match ($gender) {
                    MediaContentGender::FEMALE->value => $femaleMediaIdsCacheKey,
                    MediaContentGender::MALE->value => $maleMediaIdsCacheKey,
                    MediaContentGender::TRANSGENDER->value => $transgenderMediaIdsCacheKey,
                };
            }

            $cacheKey = config('database.redis.options.prefix').$notVisitedTopVideosCacheKey;
            Redis::rawCommand('ZDIFFSTORE', $cacheKey, count($filterBuckets) + 1, $mediaIdsCacheKey, ...$filterBuckets);
            Redis::expireAt($notVisitedTopVideosCacheKey, now()->addHour()->getTimestamp());
        }

        return array_keys(Redis::zPopMax($notVisitedTopVideosCacheKey, 5));
    }

    /**
     * @param  array   $medias
     * @param  string  $uuid
     *
     * @return void
     */
    public function watchedMedia(array $medias, string $uuid): void
    {
        $mediaIds = [];
        if (! empty($uuid) && ! empty($medias)) {
            $medias = collect(array_slice($medias, 0, 500))
                ->filter(function ($item, $key) use (&$mediaIds) {
                    if (empty($item['media_id']) || ! isset($item['duration'])) {
                        return false;
                    }

                    if (in_array($item['media_id'], $mediaIds)) {
                        return false;
                    }

                    $mediaIds[] = $item['media_id'];

                    return true;
                })->toArray();

            MediaBatchWatched::dispatch($medias, $uuid);
        }
    }

    /**
     * @param  string  $sortCategory
     * @param  string  $uuid
     *
     * @return string[]
     */
    private function generateHashKeys(string $sortCategory, string $uuid): array
    {
        $mediaPrefix = config('database.redis.options.prefix').(new Media())->getCollection();
        $mediaVisitPrefix = config('database.redis.options.prefix').(new MediaVisit())->getCollection();

        $explicitMediaIdsCacheKey = $mediaPrefix.':explicit:ids:'.$sortCategory;
        $toplessMediaIdsCacheKey = $mediaPrefix.':topless:ids:'.$sortCategory;
        $mediaIdsCacheKey = $mediaPrefix.':ids:'.$sortCategory;
        $femaleMediaIdsCacheKey = $mediaPrefix.':ids:female';
        $maleMediaIdsCacheKey = $mediaPrefix.':ids:male';
        $transgenderMediaIdsCacheKey = $mediaPrefix.':ids:transgender';
        $visitedVideoCacheKey = $mediaVisitPrefix.':visited:uuid:'.$uuid;

        return [
            $explicitMediaIdsCacheKey,
            $toplessMediaIdsCacheKey,
            $mediaIdsCacheKey,
            $femaleMediaIdsCacheKey,
            $maleMediaIdsCacheKey,
            $transgenderMediaIdsCacheKey,
            $visitedVideoCacheKey,
        ];
    }
}
