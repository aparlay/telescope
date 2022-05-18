<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Admin\Services\NoteService;
use Aparlay\Core\Events\UserReceiveAlertEvent;
use Aparlay\Core\Events\UserStatusChangedEvent;
use Aparlay\Core\Events\UserVerificationStatusChangedEvent;
use Aparlay\Core\Models\Enums\UserVerificationStatus;
use Aparlay\Payment\Events\RiskyCreditCardDetectedEvent;
use Aparlay\Payment\Events\RiskyOrderDetectedEvent;

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
                'Admin '.$event->creator->note_admin_url.
                ' changed '.$event->user->note_admin_url.
                '\'s verification status to '.UserVerificationStatus::from($event->verificationStatus)->label(),
            );
        }

        if ($event instanceof RiskyCreditCardDetectedEvent) {
            $noteService->addCustomNote(
                $event->creditCard->userObj,
                $event->creditCard->userObj,
                $event->creditCard->userObj->note_admin_url.
                '\'s unverified credit card recognized as risky with score '.
                $event->creditCard->risk_score,
            );
        }

        if ($event instanceof RiskyOrderDetectedEvent) {
            $noteService->addCustomNote(
                $event->creator,
                $event->creator,
                $event->creator->note_admin_url.
                '\'s order recognized as risky with score '.
                $event->order->risk_score,
            );
        }
    }
}
