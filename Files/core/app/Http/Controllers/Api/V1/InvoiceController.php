<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\Invoice\InvoiceService;
use App\Services\Risk\RiskScreeningService;
use App\Services\Webhook\WebhookEventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceService $invoiceService,
        private RiskScreeningService $riskScreeningService,
        private WebhookEventService $webhookEventService
    ) {
    }

    public function index(Request $request)
    {
        $user = $request->attributes->get('merchant_user');
        $status = $request->query('status');

        $query = Invoice::where('user_id', $user->id)->latest();
        if ($status) {
            $query->where('status', $status);
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->paginate(20),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'currency' => 'required|string|max:10',
            'amount' => 'required|numeric|min:0.00000001',
            'reference' => 'nullable|string|max:80',
            'chain' => 'nullable|string|in:tron,eth,bsc,bep20,ton',
            'asset' => 'nullable|string|max:20',
            'line_items' => 'nullable|array',
            'line_items.*.name' => 'required_with:line_items|string|max:255',
            'line_items.*.unit_price' => 'nullable|numeric|min:0',
            'line_items.*.quantity' => 'nullable|numeric|min:0.0001',
            'expires_at' => 'nullable|date',
        ]);

        $apiKey = $request->attributes->get('merchant_api_key');
        $user = $request->attributes->get('merchant_user');

        $invoice = $this->invoiceService->create($apiKey, $request->all());
        Log::info('invoice.created', ['invoice_id' => $invoice->id, 'user_id' => $user->id, 'amount' => $invoice->amount]);

        $this->riskScreeningService->flagHighValueInvoice($user, $invoice->id, (float) $invoice->amount);
        $this->webhookEventService->publish($user, 'invoice.created', 'invoice', $invoice->id, $invoice->toArray());

        return response()->json([
            'status' => 'success',
            'data' => $invoice,
        ], 201);
    }

    public function show(Request $request, string $id)
    {
        $user = $request->attributes->get('merchant_user');
        $invoice = Invoice::where('user_id', $user->id)
            ->where(function ($query) use ($id) {
                $query->where('id', $id)->orWhere('reference', $id);
            })
            ->with('items')
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $invoice,
        ]);
    }
}
