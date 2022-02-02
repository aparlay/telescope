<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Models\Note;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Repositories\NoteRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use MongoDB\BSON\ObjectId;

class NoteService
{
    protected NoteRepository $noteRepository;

    public function __construct()
    {
        $this->noteRepository = new NoteRepository(new Note());
    }

    /**
     * Add new note.
     *
     * @param  User|Authenticatable  $creator
     * @param  $user
     * @param  $type
     * @return void
     */
    public function addNewNote(User|Authenticatable|null $creator, User $user, int $type): void
    {
        if ($creator !== null) {
            $newNote = [
                'user' => [
                    '_id' => new ObjectId($user->_id),
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                ],
                'type' => $type,
            ];

            $this->noteRepository->store($newNote);
        }
    }
}
