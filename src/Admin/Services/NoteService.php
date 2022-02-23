<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\Note;
use Aparlay\Core\Models\User;
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
        $formattedMessage = NoteType::from($type)->message($creator, $user);
        $data = $this->prepareNoteData($creator, $user, $type, $formattedMessage);

        return $this->noteRepository->store($data);
    }

    /**
     * @param User|Authenticatable $creator
     * @param User $user
     * @param string $message
     * @return Note
     */
    public function addWarningNote(User|Authenticatable $creator, User $user, string $message): Note
    {
        $formattedMessage = NoteType::from(NoteType::WARNING_MESSAGE->value)->warningMessage($creator, $user, $message);
        $data = $this->prepareNoteData($creator, $user, NoteType::WARNING_MESSAGE->value, $formattedMessage);

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
        $formattedMessage = NoteType::from(NoteType::OTHER->value)->otherMessage($creator, $user, $message);
        $data = $this->prepareNoteData($creator, $user, NoteType::OTHER->value, $formattedMessage);

        return $this->noteRepository->store($data);
    }

    private function prepareNoteData(User|Authenticatable $creator, User $user, $type, string $message): array
    {
        return [
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
            'message' => $message,
        ];
    }
}
