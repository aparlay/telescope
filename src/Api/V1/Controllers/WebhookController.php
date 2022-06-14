<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Events\PusherClientEvent;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WebhookController extends Controller
{
    const PUSHER_EVENTS = [
        'member_added',
        'client_event',
    ];

    const PUSHER_CLIENT_EVENTS = [
        'client-message-read',
        'client-message-received',
    ];

    /**
     * Display a listing of the resource.
     */
    public function pusher(Request $request): Response|Application|ResponseFactory
    {
        // environmental variable must be set
        $appSecret = config('broadcasting.broadcaster.secret');

        $webhookSignature = $request->header('X-PUSHER-SIGNATURE', 'default');
        $expectedSignature = hash_hmac('sha256', $request->getContent(), $appSecret, false);

        abort_unless($webhookSignature === $expectedSignature, 401);

        foreach ($request->input('events') as $event) {
            if (in_array($event['name'], self::PUSHER_EVENTS, true)) {
                PusherClientEvent::dispatchIf(
                    (
                        ! isset($event['event']) ||
                        in_array($event['event'], self::PUSHER_CLIENT_EVENTS, true)
                    ),
                    $event
                );
            }
        }

        return response('', 200, []);
    }
}
