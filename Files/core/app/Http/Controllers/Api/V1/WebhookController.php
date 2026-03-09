<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\WebhookEndpoint;
use App\Services\Webhook\WebhookEventService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebhookController extends Controller
{
    public function __construct(private WebhookEventService $webhookEventService)
    {
    }

    public function register(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'name' => 'nullable|string|max:255',
            'events' => 'nullable|array',
        ]);

        $user = $request->attributes->get('merchant_user');
        $endpoint = WebhookEndpoint::create([
            'user_id' => $user->id,
            'name' => $request->name ?? 'Default endpoint',
            'url' => $request->url,
            'secret' => Str::random(64),
            'events' => $request->events ?? ['invoice.*', 'payout.*'],
            'status' => 1,
            'last_rotated_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $endpoint,
        ], 201);
    }

    public function rotateSecret(Request $request, int $id)
    {
        $user = $request->attributes->get('merchant_user');
        $endpoint = WebhookEndpoint::where('user_id', $user->id)->findOrFail($id);
        $endpoint->secret = Str::random(64);
        $endpoint->last_rotated_at = now();
        $endpoint->save();

        return response()->json(['status' => 'success', 'data' => $endpoint]);
    }

    public function test(Request $request)
    {
        $user = $request->attributes->get('merchant_user');
        $event = $this->webhookEventService->publish(
            $user,
            'webhook.test',
            'system',
            null,
            ['message' => 'Test webhook event']
        );

        return response()->json([
            'status' => 'success',
            'data' => ['event_id' => $event->event_id],
        ]);
    }
}
