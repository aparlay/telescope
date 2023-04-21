<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\Note;
use Aparlay\Core\Models\Enums\NoteCategory;
use Aparlay\Core\Models\Enums\NoteType;
use Aparlay\Core\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use MongoDB\BSON\ObjectId;

class NoteService
{
    public function __construct()
    {
    }

    public function create($userId, $type)
    {
        $user = User::findOrFail($userId);

        return Note::create([
            'user' => [
                '_id' => new ObjectId($user->_id),
                'username' => $user->username,
                'avatar' => $user->avatar,
            ],
            'type' => $type,
            'category' => ($type === NoteType::OTHER->value ? NoteCategory::NOTE->value : NoteCategory::LOG->value),
        ]);
    }

    public function delete($id)
    {
        return Note::findOrFail($id)->delete();
    }

    public function addNewNote(User|Authenticatable $creator, User $user, int $type): Note
    {
        $formattedMessage = NoteType::from($type)->message($creator, $user);
        $data             = $this->prepareNoteData($creator, $user, $type, $formattedMessage);

        return Note::create($data);
    }

    public function addWarningNote(User|Authenticatable $creator, User $user, string $message): Note
    {
        $formattedMessage = NoteType::from(NoteType::WARNING_MESSAGE->value)->warningMessage($creator, $user, $message);
        $data             = $this->prepareNoteData($creator, $user, NoteType::WARNING_MESSAGE->value, $formattedMessage);

        return Note::create($data);
    }

    /**
     * @param int $type
     */
    public function addCustomNote(User|Authenticatable $creator, User $user, string $message): Note
    {
        $formattedMessage = NoteType::from(NoteType::OTHER->value)->otherMessage($creator, $user, $message);
        $data             = $this->prepareNoteData($creator, $user, NoteType::OTHER->value, $formattedMessage);

        return Note::create($data);
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
            'category' => ($type === NoteType::OTHER->value ? NoteCategory::NOTE->value : NoteCategory::LOG->value),
            'message' => $message,
        ];
    }
}
