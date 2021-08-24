<?php

namespace Aparlay\Core\Repositories;

use Aparlay\Core\Api\V1\Controllers\Controller;
use Aparlay\Core\Api\V1\Models\Follow;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Requests\MediaRequest;
use Aparlay\Core\Api\V1\Resources\MediaResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use MongoDB\BSON\ObjectId;

class MediaRepository extends Controller
{
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

    /**
     * @param $userId
     * @return mixed
     */
    public function findByUser($userId)
    {
        $query = Media::creator($userId)->recentFirst();

        if (auth()->guest()) {
            $query->confirmed()->public();
        } elseif ((string) $userId === (string) auth()->user()->_id) {
            $query->availableForOwner();
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

        return $query->get();
    }
}
