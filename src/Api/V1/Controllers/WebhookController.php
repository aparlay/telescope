<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Events\PusherClientEvent;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WebhookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function pusher(Request $request): Response|Application|ResponseFactory
    {
        // environmental variable must be set
        $appSecret = config('broadcasting.broadcaster.secret');

        $webhookSignature = $request->header('X-PUSHER-SIGNATURE', 'default');
        $expectedSignature = hash_hmac( 'sha256', $request->getContent(), $appSecret, false );

        abort_unless($webhookSignature === $expectedSignature, 401);

        foreach ($request->input('events') as $event) {
            if (isset($event->name, $event->user_id, $event->chat_id, $event->message_id)) {
                PusherClientEvent::dispatch($event->name, $event);
            } else {
                \Log::debug('New Pusher Client Event delivered with: ' . json_encode($event));
            }
        }

        return response();
    }
}
