<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Email;
use Aparlay\Core\Events\SocketClientEvent;
use Aparlay\Core\Events\SocketClientStateEvent;
use Aparlay\Core\Models\Enums\EmailStatus;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WebhookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function socket(Request $request): Response|Application|ResponseFactory
    {
        // environmental variable must be set
        $appSecret         = config('broadcasting.broadcaster.secret');

        $webhookSignature  = $request->header('X-PUSHER-SIGNATURE', 'default');
        $expectedSignature = hash_hmac('sha256', $request->getContent(), $appSecret, false);

        abort_unless($webhookSignature === $expectedSignature, 401);

        foreach ($request->input('events', []) as $event) {
            $event['data'] = isset($event['data']) ? (is_array($event['data']) ? $event['data'] : json_decode(
                $event['data'],
                true
            )) : [];

            if (($event['name'] === 'client_event')) {
                SocketClientEvent::dispatchIf(($event['event'] !== 'client-state'), $event);
                SocketClientStateEvent::dispatchIf(($event['event'] === 'client-state'), $event);
            }
        }

        return response('', 200, []);
    }

    /**
     * Display a listing of the resource.
     */
    public function statusEmailUpdate(Request $request): Response|Application|ResponseFactory
    {
        // environmental variable must be set
        // $appSecret = config('services.postfix.secret');
        // $webhookSignature = $request->header('X-EMAIL-SIGNATURE', 'default');
        // $expectedSignature = hash_hmac('sha256', $request->getContent(), $appSecret, false);
        // abort_unless($webhookSignature === $expectedSignature, 401);

        /*
         {
              "time": "0000-11-26T21:08:41+03:25",
              "hostname": "mail1",
              "process": "postfix/submission/smtpd[115383]",
              "queue_id": "9D9863FEE1",
              "client_hostname": "unknown",
              "client_ip": "2600:3c01::f03c:93ff:fe76:dbcd",
              "message_id": "4e17a2b4399debdfaa16b807563bca9d@waptap.com",
              "from": "noreply@waptap.com",
              "messages": [
                {
                  "time": "0000-11-26T21:08:42+03:25",
                  "to": "thinny30@gmail.com",
                  "status": "bounced",
                  "message": "to=\u003cthinny30@gmail.com\u003e, relay=gmail-smtp-in.l.google.com[142.250.112.27]:25, delay=0.43, delays=0.2/0.01/0.18/0.03, dsn=5.1.1, status=bounced (host gmail-smtp-in.l.google.com[142.250.112.27] said: 550-5.1.1 The email account that you tried to reach does not exist. Please try 550-5.1.1 double-checking the recipient's email address for typos or 550-5.1.1 unnecessary spaces. Learn more at 550 5.1.1  https://support.google.com/mail/?p=NoSuchUser d65-20020a251d44000000b006f3cdde5cb1si3303772ybd.234 - gsmtp (in reply to RCPT TO command))"
                }
              ]
          }
         */
        $messages = $request->input('messages', []);
        $message  = end($messages);

        $result   = 422;
        if (isset($message['status'])) {
            $emailId = str_replace(['<', '>', '@waptap.com'], '', $request->input('message_id', ''));
            if (strlen($emailId) === 24) {
                $result = Email::query()->email($emailId)->update([
                    'server' => $request->input('hostname', ''),
                    'error' => $message['error'] ?? null,
                    'dsn' => $message['dsn']     ?? null,
                    'status_label' => $message['status'],
                    'status' => match ($message['status']) {
                        'sent' => EmailStatus::DELIVERED->value,
                        'deferred' => EmailStatus::DEFERRED->value,
                        'bounced' => EmailStatus::BOUNCED->value,
                        default => EmailStatus::FAILED->value,
                    },
                ]);
            } else {
                $result = Email::query()->to($message['to'])->sent()->update([
                    'server' => $request->input('hostname', ''),
                    'error' => $message['error'] ?? null,
                    'dsn' => $message['dsn']     ?? null,
                    'status_label' => $message['status'],
                    'status' => match ($message['status']) {
                        'sent' => EmailStatus::DELIVERED->value,
                        'deferred' => EmailStatus::DEFERRED->value,
                        'bounced' => EmailStatus::BOUNCED->value,
                        default => EmailStatus::FAILED->value,
                    },
                ]);
            }
        }

        $code     = ($result > 0) ? Response::HTTP_CREATED : Response::HTTP_OK;

        return response('', $code, []);
    }
}
