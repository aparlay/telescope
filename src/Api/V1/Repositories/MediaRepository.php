<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Requests\MediaRequest;
use Aparlay\Core\Api\V1\Services\MediaService;
use Aparlay\Core\Models\Media as BaseMedia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use MongoDB\BSON\ObjectId;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class MediaRepository implements RepositoryInterface
{
    protected Media | BaseMedia $model;

    public function __construct($model)
    {
        if (! ($model instanceof BaseMedia)) {
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
    public function store(MediaRequest $request)
    {
        $user = auth()->user();
        try {
            $model = Media::create([
                'user_id' => new ObjectId($user->_id),
                'file' => $request->input('file', ''),
                'description' => $request->input('description', ''),
                'slug' => MediaService::generateSlug(6),
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
                if (! $file->storeAs('upload', $model->file, 'local')) {
                    throw new UnprocessableEntityHttpException('Cannot upload the file.');
                }
            } elseif (empty($model->file) || ! Storage::disk('upload')->exists($model->file)) {
                throw new UnprocessableEntityHttpException('Uploaded file does not exists.');
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return null;
        }

        $model->refresh();

        return $model;
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
        $model = $this->model->media($id)->firstOrFail();

        return $model->update($data);
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
