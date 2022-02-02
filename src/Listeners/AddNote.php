<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Admin\Services\NoteService;
use Aparlay\Core\Events\GenerateNote;

class AddNote
{
    public function handle(GenerateNote $event)
    {
        \Log::error($event->type);
        $noteService = app()->make(NoteService::class);
        $noteService->addNewNote(
            $event->creator,
            $event->user,
            $event->type,
        );
    }
}
