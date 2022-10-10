<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Dto\MediaDTO;
use Aparlay\Core\Api\V1\Models\Follow;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaVisit;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Repositories\MediaRepository;
use Aparlay\Core\Api\V1\Requests\MediaRequest;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Jobs\MediaWatched;
use Aparlay\Core\Models\Enums\AlertStatus;
use Aparlay\Core\Models\Enums\MediaStatus;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use MongoDB\BSON\ObjectId;
use Psr\SimpleCache\InvalidArgumentException as InvalidArgumentExceptionAlias;
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
     * @return Media
     */
    public function create(MediaRequest $request): Media
    {
        return $this->mediaRepository->store($request);
    }

    /**
     * @param Media $media
     * @param MediaDTO $mediaDto
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
     * @param  Media  $media
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function deleteAllMediasBelongToUser(): void
    {
        foreach (Media::user($this->getUser()->_id)->lazy() as $media) {
            if ($media->status !== MediaStatus::USER_DELETED->value) {
                $this->mediaRepository->update(['status' => MediaStatus::USER_DELETED->value], $media->_id);
            }
        }
    }

    /**
     * @return LengthAwarePaginator
     * @throws InvalidArgumentExceptionAlias
     */
    public function getPublicFeeds(): LengthAwarePaginator
    {
        $query = Media::query();
        $query->public()->confirmed()->sort();

        $deviceId = request()->header('X-DEVICE-ID', '');
        $cacheKey = (new MediaVisit())->getCollection().':'.$deviceId;
        $originalQuery = $query;
        $originalData = $originalQuery->paginate(5, ['*'], 'page', 1)->withQueryString();

        $userId = null;
        if (! auth()->guest()) {
            $userId = auth()->user()->_id;
            $query->notBlockedFor(auth()->user()->_id)->notVisitedByUserAndDevice($userId, $deviceId);
        } else {
            $query->notVisitedByDevice($deviceId);
        }

        $data = $query->paginate(5)->withQueryString();

        if ($data->isEmpty() || $data->total() <= 5) {
            if (! auth()->guest()) {
                MediaVisit::query()->user(auth()->user()->_id)->delete();
            }
            Cache::store('redis')->delete($cacheKey);

            if ($data->isEmpty()) {
                $data = $originalData;
            }
        }

        $visited = Cache::store('redis')->get($cacheKey, []);
        $visitCounts = [];
        foreach ($data->items() as $model) {
            $visited[] = $model->_id;
            $visitCounts[] = new ObjectId($model->_id);
        }
        MediaWatched::dispatch($visitCounts, 60, $userId);
        Cache::store('redis')->set($cacheKey, array_unique($visited, SORT_REGULAR), config('app.cache.veryLongDuration'));

        return $data;
    }

    /**
     * @param  string  $type
     * @return LengthAwarePaginator
     * @throws Exception
     */
    public function getFeedByType(string $type): LengthAwarePaginator
    {
        return match ($type) {
            'following' => $this->getFollowingFeed(),
            default => $this->getFollowingFeed(),
        };
    }

    /**
     * @return LengthAwarePaginator
     * @throws Exception
     */
    public function getFollowingFeed(): LengthAwarePaginator
    {
        $query = Media::query();

        if (! auth()->guest()) {
            $query->availableForFollower()
                ->following(auth()->user()->_id)
                ->notBlockedFor(auth()->user()->_id)
                ->recentFirst();
        }

        $data = $query->paginate(5)->withQueryString();
        $visitCounts = [];
        foreach ($data->items() as $model) {
            $visitCounts[] = new ObjectId($model->_id);
        }
        $userId = auth()->guest() ? null : auth()->user()->_id;

        MediaWatched::dispatch($visitCounts, 60, $userId);

        return $data;
    }

    /**
     * @param  User  $user
     * @return LengthAwarePaginator
     * @throws InvalidArgumentExceptionAlias
     */
    public function getByUser(User $user): LengthAwarePaginator
    {
        $userId = $user->_id;
        $query = Media::creator($userId)->recentFirst();

        if (auth()->guest()) {
            $query->confirmed()->public();
        } elseif ((string) $userId === (string) auth()->user()->_id) {
            $query->with(['alertObjs' => function ($query) {
                $query->where('status', AlertStatus::NOT_VISITED->value);
            }])->availableForOwner();
        } else {
            $isFollowed = Follow::query()
                ->select(['user._id', '_id'])
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

    /**
     * @param  Media          $media
     * @param  int            $duration
     * @param  ObjectId|null  $userId
     *
     * @return void
     * @throws Exception
     */
    public function watched(Media $media, int $duration = 60, ObjectId|null $userId = null): void
    {
        if ($userId !== null) {
            $this->userWatched($userId, $media, $duration);
        } else {
            $this->anonymousWatched($media, $duration);
        }
    }

    /**
     * @param  Media  $media
     * @param  int    $duration
     *
     * @return void
     */
    public function anonymousWatched(Media $media, int $duration = 60): void
    {
        if ($duration > 3) {
            $multiplier = config('app.media.visit_multiplier', 7);
            $media->length_watched += ((($duration > $media->length) ? $media->length : $duration) * $multiplier);
            $media->visit_count += $multiplier;
            $media->count_fields_updated_at = array_merge(
                $media->count_fields_updated_at,
                ['visits' => DT::utcNow()]
            );
            $media->save();
        }
    }

    /**
     * @param  ObjectId  $userId
     * @param  Media     $media
     * @param  int       $duration
     *
     * @return void
     * @throws Exception
     */
    public function userWatched(ObjectId $userId, Media $media, int $duration = 60): void
    {
        if (($mediaVisit = MediaVisit::query()->user($userId)->dateString(date('Y-m-d'))->first()) === null) {
            $mediaVisit = new MediaVisit();
            $mediaVisit->date = date('Y-m-d');
            $mediaVisit->user_id = $userId;
        }

        $mediaVisit->media_id = new ObjectId($media->_id);
        $mediaVisit->duration = $duration;

        if ($duration > ($media->length / 4)) {
            $multiplier = config('app.media.visit_multiplier', 7);
            $media->length_watched += ((($duration > $media->length) ? $media->length : $duration) * $multiplier);
            $media->visit_count += $multiplier;
            $media->addToSet('visits', [
                '_id' => $mediaVisit->userObj->_id,
                'username' => $mediaVisit->userObj->username,
                'avatar' => $mediaVisit->userObj->avatar,
            ], 10);
            $media->count_fields_updated_at = array_merge(
                $media->count_fields_updated_at,
                ['visits' => DT::utcNow()]
            );
            $media->save();
        }

        if (! $mediaVisit->save()) {
            throw new Exception('Cannot save media visit data.');
        }
    }
}
