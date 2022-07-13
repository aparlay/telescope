<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Mail\EmailEnvelope;
use Aparlay\Core\Models\Email;
use Aparlay\Core\Models\Email as EmailModel;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\ThirdPartyLogger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CoreCommand extends Command
{
    public $signature = 'core:index';

    public $description = 'Aparlay Core Command';

    public function handle()
    {
        // https://respina24.info/flight/getFlightAjax?source[]=MHD&destination[]=THR&DepartureGo=2022-07-22&type=&version=1657689802
        $response = Http::timeout(180)
            ->retry(5, 60000)
            ->get('https://respina24.info/flight/getFlightAjax?source[]=MHD&destination[]=THR&DepartureGo=2022-07-22&type=&version=1657689802');

        if ($response->successful() && ! empty($response['data'])) {
            foreach ($response['data'] as $flight) {
                if ((int) substr($flight['takeoffTime'], 0, 2) >= 5 && (int) $flight['num'] > 1) {
                    $flight['msg'] = json_encode($flight, JSON_PRETTY_PRINT);

                    \Aparlay\Core\Jobs\Email::dispatch('ramin.farmani@gmail.com', 'New Flight Detected', EmailModel::TEMPLATE_EMAIL_CONTACTUS, $flight);
                }
            }
        }

        $this->comment('All done');

        return self::SUCCESS;
    }
}
