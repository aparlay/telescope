<?php

namespace Aparlay\Core\Repositories;

use Aparlay\Core\Api\V1\Controllers\Controller;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Requests\MediaRequest;
use Aparlay\Core\Api\V1\Resources\MediaResource;
use Aparlay\Core\Models\MediaVisit;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use MongoDB\BSON\ObjectId;

class MediaRepository extends Controller
{
    /**
     * @param string|null $type
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getByType(string $type): mixed
    {
        $query = Media::query();
        if (! auth()->guest() && $type === 'following') {
            $query->availableForFollower()->following(auth()->user()->_id)->latest();
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
                    MediaVisit::user(auth()->user()->_id)->delete();
                }

                cache()->delete($cacheKey);

                redirect('index');
            }
            $provider = $query->paginate(15);
        } else {
            $provider = $query->get();
        }

        $visited = cache()->has($cacheKey) ? cache()->get($cacheKey) : [];
        foreach ($provider as $model) {
            $visited[] = $model->_id;
        }

        cache()->set($cacheKey, array_unique($visited, SORT_REGULAR), config('app.cache.veryLongDuration'));

        return $provider;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MediaRequest $request
     * @return Response
     */
    public function store(MediaRequest $request): Response
    {
        $user = auth()->user();

        $media = new Media([
           'visibility'  => $request->input('visibility', 0),
           'creator'     => [
               '_id'      => new ObjectId($user->_id),
               'username' => $user->username,
               'avatar'   => $user->avatar,
           ],
           'user_id' => new ObjectId($user->_id),
           'description' => $request->input('description'),
       ]);

        if ($request->hasFile('file')) {
            $file = $request->file;

            $media->file = uniqid('tmp_', true).'.'.$file->extension();
            $path = Storage::path('upload').'/'.$media->file;

            if (! $file->storeAs('upload', $path)) {
                $this->error(__('Cannot upload the file.'));
            }
        } elseif (! empty($media->file)
            && ! file_exists(Storage::path('upload').'/'.$media->file)) {
            $this->error(__('Uploaded file does not exists.'));
        }

        $media->save();
        $media->refresh();

        return $this->response(new MediaResource($media), '', Response::HTTP_CREATED);
    }
}
