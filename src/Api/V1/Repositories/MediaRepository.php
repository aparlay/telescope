<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\MediaRequest;
use Aparlay\Core\Api\V1\Services\MediaService;
use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Media as BaseMedia;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use MongoDB\BSON\ObjectId;

class MediaRepository
{
    protected Media|BaseMedia $model;

    public function __construct($model)
    {
        if (!($model instanceof BaseMedia)) {
            throw new InvalidArgumentException('$model should be of Media type');
        }

        $this->model = $model;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Media|null
     */
    public function store(MediaRequest $request)
    {
        $user = User::user(auth()->user()->_id)->firstOrFail();
        try {
            $model = Media::create([
                'status' => MediaStatus::QUEUED->value,
                'user_id' => new ObjectId($user->_id),
                'file' => $request->input('file', ''),
                'description' => $request->input('description', ''),
                'slug' => MediaService::generateSlug(6),
                'is_comments_enabled' => true,
                'count_fields_updated_at' => [],
                'visibility' => $request->input('visibility', $user->visibility),
                'creator' => [
                    '_id' => new ObjectId($user->_id),
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                ],
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return;
        }

        $model->refresh();

        return $model;
    }

    public function update(array $data, $id)
    {
        $model = $this->model->media($id)->firstOrFail();

        return $model->update($data);
    }
}
