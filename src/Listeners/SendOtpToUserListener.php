<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Api\V1\Services\OtpService;
use Aparlay\Core\Events\UserOtpRequestedEvent;

class SendOtpToUserListener
{
    public function __construct(
        private OtpService $otpService
    ) {
    }

    public function handle(UserOtpRequestedEvent $event)
    {
        $this->otpService->sendOtp($event->getUser(), $event->getDeviceId());
    }
}
