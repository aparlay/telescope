<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Payment\Events\RiskyUserDetectedEvent;

class RiskyUserDetectorListener
{
    /**
     * @param RiskyUserDetectedEvent $event
     * @return void
     */
    public function handle($event)
    {
        $setting = $event->user->setting;
        $setting['payment']['allow_unverified_cc'] = false;
        $setting['payment']['block_unverified_cc'] = true;
        $setting['payment']['block_cc_payments'] = true;
        $event->user->setting = $setting;
        $event->user->save();
    }
}
