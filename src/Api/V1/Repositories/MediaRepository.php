<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Requests\MediaRequest;
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
     * @return Media
     */
    public function store(MediaRequest $request): Media
    {
        $user = auth()->user();

        try {
            $this->model->visibility = $request->input('visibility', 0);
            $this->model->creator = [
                '_id'      => new ObjectId($user->_id),
                'username' => $user->username,
                'avatar'   => $user->avatar,
            ];
            $this->model->user_id = new ObjectId($user->_id);
            $this->model->description = $request->input('description');
            $this->model->count_fields_updated_at = [];

            if ($request->hasFile('file')) {
                $file = $request->file;

                $this->model->file = uniqid('tmp_', true).'.'.$file->extension();
                $path = Storage::path('upload').'/'.$this->model->file;

                if (! $file->storeAs('upload', $path)) {
                    $this->error(__('Cannot upload the file.'));
                }
            } elseif (! empty($this->model->file)
                && ! file_exists(Storage::path('upload').'/'.$this->model->file)) {
                $this->error(__('Uploaded file does not exists.'));
            }
            $this->model->save();
            $this->model->refresh();

            return $this->model;
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return null;
        }

        // $media = new Media([
        //    'visibility'  => $request->input('visibility', 0),
        //    'creator'     => [
        //        '_id'      => new ObjectId($user->_id),
        //        'username' => $user->username,
        //        'avatar'   => $user->avatar,
        //    ],
        //    'user_id' => new ObjectId($user->_id),
        //    'description' => $request->input('description'),
        //    'count_fields_updated_at' => [],
        // ]);

        // if ($request->hasFile('file')) {
        //     $file = $request->file;

        //     $media->file = uniqid('tmp_', true).'.'.$file->extension();
        //     $path = Storage::path('upload').'/'.$media->file;

        //     if (! $file->storeAs('upload', $path)) {
        //         $this->error(__('Cannot upload the file.'));
        //     }
        // } elseif (! empty($media->file)
        //     && ! file_exists(Storage::path('upload').'/'.$media->file)) {
        //     $this->error(__('Uploaded file does not exists.'));
        // }
        // $media->save();
        // $media->refresh();

        // return $media;
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
