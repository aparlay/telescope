<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Models\Follow;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Requests\MediaRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use MongoDB\BSON\ObjectId;

class MediaRepository implements RepositoryInterface
{
    protected Media $model;

    public function __construct($model)
    {
        if (! ($model instanceof Media)) {
            throw new \InvalidArgumentException('$model should be of Media type');
        }

        $this->model = $model;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MediaRequest $request
     * @return Media|null
     */
    public function store(MediaRequest $request): ?Media
    {
        $user = auth()->user();
        try {
            $model = Media::create([
                'user_id' => new ObjectId($user->_id),
                'file' => $request->input('file', ''),
                'description' => $request->input('description', ''),
                'count_fields_updated_at' => [],
                'visibility' => $request->input('visibility', $user->visibility),
                'creator' => [
                    '_id'      => new ObjectId($user->_id),
                    'username' => $user->username,
                    'avatar'   => $user->avatar,
                ],
            ]);

            if ($request->hasFile('file')) {
                $file = $request->file;
                $model->file = uniqid('tmp_', true).'.'.$file->extension();
                $path = Storage::path('upload').'/'.$model->file;

                if (! $file->storeAs('upload', $path)) {
                    Log::error('Cannot upload the file.');
                }
            } elseif (! empty($model->file) && ! file_exists(Storage::path('upload').'/'.$model->file)) {
                Log::error('Uploaded file does not exists.');
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return null;
        }

        $model->refresh();

        return $this->model;
    }

    public function all()
    {
        // TODO: Implement all() method.
    }

    public function create(array $data)
    {
        // TODO: Implement create() method.
    }

    public function update(array $data, $id)
    {
        // TODO: Implement update() method.
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }
}
