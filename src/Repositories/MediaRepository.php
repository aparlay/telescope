<?php

namespace Aparlay\Core\Repositories;

use Aparlay\Core\Api\V1\Requests\MediaRequest;
use Aparlay\Core\Models\Media;
use Illuminate\Support\Facades\Storage;
use MongoDB\BSON\ObjectId;

class MediaRepository implements RepositoryInterface
{
    /**
     * Store a newly created resource in storage.
     *
     * @param MediaRequest $request
     * @return Media
     */
    public function store(MediaRequest $request): Media
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
           'count_fields_updated_at' => [],
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

        return $media;
    }

    public function __construct($model)
    {
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
