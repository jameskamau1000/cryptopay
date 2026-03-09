<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PluginController extends Controller
{
    public function handshake(Request $request)
    {
        $request->validate([
            'platform' => 'required|string|max:50',
            'platform_version' => 'nullable|string|max:40',
            'plugin_version' => 'nullable|string|max:40',
            'callback_url' => 'nullable|url',
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'platform' => $request->platform,
                'compatibility' => 'supported',
                'min_supported_api' => 'v1',
                'recommended_endpoints' => [
                    'create_invoice' => url('/api/v1/invoices'),
                    'check_invoice' => url('/api/v1/invoices/{id}'),
                    'webhook_test' => url('/api/v1/webhooks/test'),
                ],
            ],
        ]);
    }
}
