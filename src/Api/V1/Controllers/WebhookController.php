<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Email;
use Aparlay\Core\Events\SocketClientEvent;
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
        $appSecret = config('broadcasting.broadcaster.secret');

        $webhookSignature = $request->header('X-PUSHER-SIGNATURE', 'default');
        $expectedSignature = hash_hmac('sha256', $request->getContent(), $appSecret, false);

        abort_unless($webhookSignature === $expectedSignature, 401);

        foreach ($request->input('events', []) as $event) {
            $event['data'] = isset($event['data']) ? (is_array($event['data']) ? $event['data'] : json_decode($event['data'], true)) : [];
            SocketClientEvent::dispatchIf(($event['name'] === 'client_event'), $event);
        }

        return response('', 200, []);
    }

    /**
     * Display a listing of the resource.
     */
    public function statusEmailUpdate(Email $email, Request $request): Response|Application|ResponseFactory
    {
        // environmental variable must be set
        $appSecret = config('services.mailgun.secret');

        $webhookSignature = $request->header('X-EMAIL-SIGNATURE', 'default');
        $expectedSignature = hash_hmac('sha256', $request->getContent(), $appSecret, false);

        abort_unless($webhookSignature === $expectedSignature, 401);

        foreach ($request->input('events', []) as $event) {
            $event['data'] = isset($event['data']) ? (is_array($event['data']) ? $event['data'] : json_decode($event['data'], true)) : [];
            SocketClientEvent::dispatchIf(($event['name'] === 'client_event'), $event);
        }

        return response('', 200, []);
    }
}
