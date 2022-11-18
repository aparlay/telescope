<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Dto\MediaDTO;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\MediaRequest;
use Aparlay\Core\Api\V1\Requests\UpdateMediaRequest;
use Aparlay\Core\Api\V1\Resources\MediaCollection;
use Aparlay\Core\Api\V1\Resources\MediaFeedsCollection;
use Aparlay\Core\Api\V1\Resources\MediaResource;
use Aparlay\Core\Api\V1\Services\MediaService;
use Aparlay\Core\Api\V1\Services\UploadService;
use Aparlay\Core\Jobs\MediaBatchWatched;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;

class MediaController extends Controller
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
        $this->authorizeResource(Media::class, 'media');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        if (($type = request()?->input('type')) !== null) {
            $collection = new MediaCollection($this->mediaService->getFeedByType($type));
        } else {
            $collection = new MediaFeedsCollection($this->mediaService->getPublicFeeds());
        }

        return $this->response($collection, '', Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     */
    public function listByUser(User $user): Response
    {
        return $this->response(new MediaCollection($this->mediaService->getByUser($user)), '', Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MediaRequest $request
     * @return Response
     */
    public function store(MediaRequest $request): Response
    {
        return $this->response(
            new MediaResource($this->mediaService->create($request)),
            '',
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Media $media): Response
    {
        return $this->response(new MediaResource($media));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMediaRequest $request, Media $media): Response
    {
        return $this->response(
            new MediaResource($this->mediaService->update($media, MediaDTO::fromRequest($request))),
            '',
            Response::HTTP_CREATED
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $media): Response
    {
        $this->mediaService->delete($media);

        return $this->response([], '', Response::HTTP_NO_CONTENT);
    }

    /**
     * Upload media file.
     *
     * @param  Request  $request
     * @return Response
     */
    public function streamUpload(Request $request)
    {
        $result = UploadService::stream($request);

        return response($result['data'], $result['code'], []);
    }

    /**
     * Upload media file.
     *
     * @param  Request  $request
     * @return Response
     * @throws \Flow\FileLockException
     * @throws \Flow\FileOpenException
     */
    public function splitUpload(Request $request): Response
    {
        $result = UploadService::split($request);

        return response($result['data'], $result['code'], []);
    }

    /**
     * @param  Request  $request
     *
     * @return Response
     * @throws \RedisException
     */
    public function watched(Request $request): Response
    {
        $deviceId = $request->header('X-DEVICE-ID', '');
        $cacheKey = 'tracking:media:watched:'.date('Y:m:d:').$deviceId;
        $medias = $request->all();
        $mediaIds = [];
        if (!empty($deviceId) && !empty($medias)) {
            $medias = collect(array_slice($medias, 0, 500))->filter(function ($item, $key) use (&$mediaIds, $cacheKey) {
                if (empty($item['media_id']) || empty($item['duration'])) {
                    return false;
                }

                if (Redis::sismember($cacheKey, $item['media_id'])) {
                    return false;
                }

                if (in_array($item['media_id'], $mediaIds)) {
                    return false;
                }

                $mediaIds[] = $item['media_id'];
                return true;
            })->toArray();

            if (Redis::exists($cacheKey)) {
                Redis::expireat($cacheKey, now()->addDay()->startOfDay()->getTimestamp());
            }
            Redis::sAdd($cacheKey, ...$mediaIds);
            MediaBatchWatched::dispatch($medias);
        }

        return response('', 202, []);
    }
}
