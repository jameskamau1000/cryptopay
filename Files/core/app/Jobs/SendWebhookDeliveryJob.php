<?php

namespace App\Jobs;

use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use App\Models\WebhookEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendWebhookDeliveryJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $deliveryId)
    {
    }

    public function handle(): void
    {
        $delivery = WebhookDelivery::find($this->deliveryId);
        if (!$delivery || $delivery->status === 'delivered') {
            return;
        }

        $event = WebhookEvent::find($delivery->webhook_event_id);
        $endpoint = WebhookEndpoint::find($delivery->webhook_endpoint_id);

        if (!$event || !$endpoint || !$endpoint->status) {
            return;
        }

        $payload = [
            'id' => $event->event_id,
            'type' => $event->event_type,
            'resource_type' => $event->resource_type,
            'resource_id' => $event->resource_id,
            'data' => $event->payload,
            'sent_at' => now()->toIso8601String(),
        ];

        $rawPayload = json_encode($payload);
        $signature = hash_hmac('sha256', $rawPayload, $endpoint->secret);
        $ch = curl_init($endpoint->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-CryptoPay-Event: ' . $event->event_id,
            'X-CryptoPay-Signature: ' . $signature,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $rawPayload);
        $response = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        $delivery->attempts += 1;
        $delivery->response_body = $response ?: $error;
        $delivery->response_code = $code ?: null;

        if ($code >= 200 && $code < 300) {
            $delivery->status = 'delivered';
            $delivery->delivered_at = now();
        } elseif ($delivery->attempts >= 5) {
            $delivery->status = 'dead_letter';
            $delivery->next_retry_at = null;
        } else {
            $delivery->status = 'retrying';
            $delivery->next_retry_at = now()->addMinutes((int) pow(2, $delivery->attempts));
            self::dispatch($delivery->id)->delay($delivery->next_retry_at);
        }

        $delivery->save();
    }
}
