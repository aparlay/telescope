<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Admin\Services\NoteService;
use Aparlay\Core\Events\UserStatusChanged;

class AddNote
{
    public function handle(UserStatusChanged $event)
    {
        $noteService = app()->make(NoteService::class);
        $noteService->addNewNote(
            $event->creator,
            $event->user,
            $event->type,
        );
    }
}
