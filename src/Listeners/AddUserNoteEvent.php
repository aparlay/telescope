<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Services\NoteService;
use Aparlay\Core\Events\UserStatusChanged;
use Aparlay\Core\Models\Enums\NoteType;
use Aparlay\Core\Models\Enums\UserStatus;


class AddUserNoteEvent
{

    public function handle(UserStatusChanged $event)
    {
        \Log::info('e', [
            'event' => $event
        ]);

        $creatorUser = $event->getCreator();
        $userForNote = User::find($event->getUserId());
        $newUserStatus = $event->getNewUserStatus();

        $type = match ($newUserStatus) {
            UserStatus::BLOCKED->value => NoteType::BAN->value,
            UserStatus::ACTIVE->value => NoteType::UNBAN->value,
        };

        $noteService = app()->make(NoteService::class);

        $noteService->addNewNote(
            $creatorUser,
            $userForNote,
            $type,
        );
    }
}
