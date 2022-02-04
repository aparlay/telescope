<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Admin\Services\NoteService;
use Aparlay\Core\Events\UserReceiveAlertEvent;
use Aparlay\Core\Events\UserStatusChanged;

class AddNote
{
    public function handle($event)
    {
        $noteService = app()->make(NoteService::class);

        if ($event instanceof UserStatusChanged) {
            $noteService->addNewNote(
                $event->creator,
                $event->user,
                $event->type,
            );
        }
        if($event instanceof UserReceiveAlertEvent) {
            $noteService->addWarningNote(
                $event->creator,
                $event->user,
                $event->getMessage()
            );
        }


    }
}
