<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\Note;
use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Repositories\NoteRepository;
use MongoDB\BSON\ObjectId;
use Aparlay\Core\Models\Enums\NoteType;

class NoteService
{
    protected NoteRepository $noteRepository;

    public function __construct()
    {
        $this->noteRepository = new NoteRepository(new Note());
    }

    public function create()
    {
        $data = request()->only(['user_id', 'type', 'message']);
        $user = User::findOrFail($data['user_id']);
        $createData = [
            'user' => [
                '_id' => new ObjectId($user->_id),
                'username' => $user->username,
                'avatar' => $user->avatar,
            ],
            'type' => $data['type'],
            'message' => $data['message'],
        ];

        return $this->noteRepository->store($createData);
    }

    public function delete($id)
    {
        return $this->noteRepository->delete($id);
    }
}
