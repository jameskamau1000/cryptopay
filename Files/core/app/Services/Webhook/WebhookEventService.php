<?php

namespace App\Services\Webhook;

use App\Jobs\SendWebhookDeliveryJob;
use App\Models\User;
use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use App\Models\WebhookEvent;
use Illuminate\Support\Str;

class WebhookEventService
{
    public function publish(User $user, string $eventType, string $resourceType, ?int $resourceId, array $payload): WebhookEvent
    {
        $event = WebhookEvent::create([
            'user_id' => $user->id,
            'event_id' => 'evt_' . Str::lower(Str::random(24)),
            'event_type' => $eventType,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'payload' => $payload,
            'published_at' => now(),
        ]);

        $endpoints = WebhookEndpoint::where('user_id', $user->id)->where('status', 1)->get();
        foreach ($endpoints as $endpoint) {
            $delivery = WebhookDelivery::create([
                'webhook_event_id' => $event->id,
                'webhook_endpoint_id' => $endpoint->id,
                'status' => 'queued',
                'attempts' => 0,
                'next_retry_at' => now(),
            ]);

            SendWebhookDeliveryJob::dispatch($delivery->id);
        }

        return $event;
    }
}
