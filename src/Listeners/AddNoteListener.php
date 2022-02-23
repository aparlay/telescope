<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Admin\Services\NoteService;
use Aparlay\Core\Events\UserReceiveAlertEvent;
use Aparlay\Core\Events\UserStatusChangedEvent;
use Aparlay\Core\Events\UserVerificationStatusChangedEvent;
use Aparlay\Core\Models\Enums\UserVerificationStatus;

class AddNoteListener
{
    public function handle($event)
    {
        $noteService = app()->make(NoteService::class);

        if ($event instanceof UserStatusChangedEvent) {
            $noteService->addNewNote(
                $event->creator,
                $event->user,
                $event->type,
            );
        }
        if ($event instanceof UserReceiveAlertEvent) {
            $noteService->addWarningNote(
                $event->creator,
                $event->user,
                $event->getMessage()
            );
        }
        if ($event instanceof UserVerificationStatusChangedEvent) {
            $noteService->addCustomNote(
                $event->creator,
                $event->user,
                'Admin ' . $event->creator->note_admin_url . ' changed ' . $event->user->note_admin_url . '\'s verification status to ' . UserVerificationStatus::from($event->verificationStatus)->label(),
            );
        }
    }
}
