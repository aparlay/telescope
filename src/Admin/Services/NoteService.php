<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\Note;
use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Repositories\NoteRepository;
use Aparlay\Core\Models\Enums\NoteType;
use Illuminate\Contracts\Auth\Authenticatable;
use MongoDB\BSON\ObjectId;

class NoteService
{
    protected NoteRepository $noteRepository;

    public function __construct()
    {
        $this->noteRepository = new NoteRepository(new Note());
    }

    public function create($userId, $type)
    {
        $user = User::findOrFail($userId);
        $createData = [
            'user' => [
                '_id' => new ObjectId($user->_id),
                'username' => $user->username,
                'avatar' => $user->avatar,
            ],
            'type' => $type,
        ];

        return $this->noteRepository->store($createData);
    }

    public function delete($id)
    {
        return $this->noteRepository->delete($id);
    }

    /**
     * @param User|Authenticatable $creator
     * @param User $user
     * @param int $type
     * @return Note
     */
    public function addNewNote(User|Authenticatable $creator, User $user, int $type): Note
    {
        $data = [
            'creator' => [
                '_id' => new ObjectId($creator->_id),
                'username' => $creator->username,
                'avatar' => $creator->avatar,
            ],
            'user' => [
                '_id' => new ObjectId($user->_id),
                'username' => $user->username,
                'avatar' => $user->avatar,
            ],
            'type' => $type,
            'message' => NoteType::from($type)->message($creator, $user),
        ];

        return $this->noteRepository->store($data);
    }


    /**
     * @param User|Authenticatable $creator
     * @param User $user
     * @param int $type
     * @return Note
     */
    public function addCustomNote(User|Authenticatable $creator, User $user, string $message): Note
    {
        $data = [
            'creator' => [
                '_id' => new ObjectId($creator->_id),
                'username' => $creator->username,
                'avatar' => $creator->avatar,
            ],
            'user' => [
                '_id' => new ObjectId($user->_id),
                'username' => $user->username,
                'avatar' => $user->avatar,
            ],
            'type' => NoteType::OTHER->value,
            'message' => NoteType::from(NoteType::OTHER->value)->otherMessage($creator, $user, $message),
        ];

        return $this->noteRepository->store($data);
    }

}
