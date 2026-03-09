<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ApiRequestLog;
use App\Models\Invoice;
use App\Models\MerchantApiKey;
use App\Models\MerchantDepositAddress;
use App\Models\OnchainDeposit;
use App\Models\OnchainPayout;
use App\Models\Payout;
use App\Models\PayoutBatch;
use App\Models\Transaction;
use App\Models\WebhookDelivery;
use App\Services\Blockchain\ChainManager;
use App\Services\Invoice\InvoiceService;
use App\Services\Payout\PayoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MerchantPortalController extends Controller
{
    public function __construct(
        private PayoutService $payoutService,
        private InvoiceService $invoiceService,
        private ChainManager $chainManager
    ) {
    }

    public function accounts()
    {
        $pageTitle = 'Accounts';
        $userId = auth()->id();

        $received = OnchainDeposit::where('user_id', $userId)
            ->selectRaw('UPPER(asset) as asset, SUM(amount) as total')
            ->groupBy('asset')
            ->pluck('total', 'asset');

        $sent = OnchainPayout::where('user_id', $userId)
            ->whereIn('status', ['broadcasted', 'confirmed', 'completed'])
            ->selectRaw('UPPER(asset) as asset, SUM(amount) as total')
            ->groupBy('asset')
            ->pluck('total', 'asset');

        $assets = collect($received->keys())->merge($sent->keys())->unique()->values();
        if ($assets->isEmpty()) {
            $assets = collect(['USDT']);
        }

        $accounts = $assets->map(function ($asset) use ($received, $sent, $userId) {
            $incoming = (float) ($received[$asset] ?? 0);
            $outgoing = (float) ($sent[$asset] ?? 0);
            $channels = MerchantDepositAddress::where('user_id', $userId)
                ->where('asset', $asset)
                ->count();

            return [
                'asset' => $asset,
                'incoming' => $incoming,
                'outgoing' => $outgoing,
                'net' => $incoming - $outgoing,
                'channels' => $channels,
            ];
        });

        $totalNet = $accounts->sum('net');

        return view(activeTemplate() . 'user.merchant.accounts', compact('pageTitle', 'accounts', 'totalNet'));
    }

    public function paymentLinks(Request $request)
    {
        $pageTitle = 'Payment Links';
        $query = Invoice::where('user_id', auth()->id())->with('depositAddress')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', '%' . $search . '%')
                    ->orWhere('external_reference', 'like', '%' . $search . '%');
            });
        }

        $invoices = $query->paginate(getPaginate())->withQueryString();
        $stats = [
            'total' => Invoice::where('user_id', auth()->id())->count(),
            'paid' => Invoice::where('user_id', auth()->id())->where('status', 'paid')->count(),
            'pending' => Invoice::where('user_id', auth()->id())->whereNotIn('status', ['paid', 'cancelled', 'expired'])->count(),
        ];

        return view(activeTemplate() . 'user.merchant.payment_links', compact('pageTitle', 'invoices', 'stats'));
    }

    public function createPaymentLink(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'currency' => 'required|string|max:20',
            'settlement_currency' => 'nullable|string|max:20',
            'chain' => 'required|in:tron,eth,bsc,ton,bep20,ethereum',
            'asset' => 'nullable|string|max:20',
            'type' => 'required|in:one_time,reusable',
            'reference' => 'nullable|string|max:80',
        ]);

        $user = auth()->user();
        $apiKey = MerchantApiKey::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'Dashboard Key'],
            [
                'public_key' => 'pk_live_' . Str::lower(Str::random(24)),
                'secret_key' => 'sk_live_' . Str::lower(Str::random(36)),
                'scopes' => ['invoices:write', 'invoices:read', 'payouts:write', 'payouts:read'],
                'is_test' => 0,
                'status' => 1,
            ]
        );

        $invoice = $this->invoiceService->create($apiKey, [
            'amount' => (float) $request->amount,
            'currency' => strtoupper($request->currency),
            'settlement_currency' => strtoupper($request->settlement_currency ?: 'USDT'),
            'chain' => strtolower($request->chain),
            'asset' => strtoupper($request->asset ?: 'USDT'),
            'type' => $request->type,
            'reference' => $request->reference ?: null,
            'metadata' => [
                'created_from' => 'merchant_dashboard',
            ],
        ]);

        $notify[] = ['success', 'Payment link created: ' . $invoice->reference];
        return back()->withNotify($notify);
    }

    public function channels(Request $request)
    {
        $pageTitle = 'Channels';
        $query = MerchantDepositAddress::where('user_id', auth()->id())->latest();

        if ($request->filled('chain')) {
            $query->where('chain', strtolower($request->chain));
        }
        if ($request->filled('asset')) {
            $query->where('asset', strtoupper($request->asset));
        }
        if ($request->filled('search')) {
            $query->where('address', 'like', '%' . $request->search . '%');
        }

        $channels = $query->paginate(getPaginate())->withQueryString();
        return view(activeTemplate() . 'user.merchant.channels', compact('pageTitle', 'channels'));
    }

    public function createChannel(Request $request)
    {
        $request->validate([
            'chain' => 'required|in:tron,eth,bsc,ton,bep20,ethereum',
            'asset' => 'nullable|string|max:20',
        ]);

        $chain = strtolower($request->chain);
        $chain = $chain === 'bep20' ? 'bsc' : $chain;
        $chain = $chain === 'ethereum' ? 'eth' : $chain;
        $asset = strtoupper($request->asset ?: 'USDT');

        try {
            $generated = $this->chainManager->for($chain)->generateAddress([
                'merchant_id' => auth()->id(),
                'asset' => $asset,
                'channel' => true,
            ]);

            MerchantDepositAddress::create([
                'user_id' => auth()->id(),
                'invoice_id' => null,
                'chain' => $chain,
                'asset' => $asset,
                'address' => $generated['address'],
                'memo' => $generated['memo'] ?? null,
                'status' => 'assigned',
                'assigned_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $notify[] = ['error', 'Unable to create channel: ' . $e->getMessage()];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Channel created successfully'];
        return back()->withNotify($notify);
    }

    public function massPayouts()
    {
        $pageTitle = 'Mass Payouts';
        $batches = PayoutBatch::where('user_id', auth()->id())
            ->withCount('items')
            ->latest()
            ->paginate(getPaginate());

        return view(activeTemplate() . 'user.merchant.mass_payouts', compact('pageTitle', 'batches'));
    }

    public function uploadMassPayoutCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
            'default_chain' => 'nullable|in:tron,eth,bsc,ton,bep20,ethereum',
            'default_asset' => 'nullable|string|max:20',
        ]);

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');

        if (!$handle) {
            $notify[] = ['error', 'Unable to read uploaded CSV file'];
            return back()->withNotify($notify);
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            $notify[] = ['error', 'CSV is empty'];
            return back()->withNotify($notify);
        }

        $normalized = array_map(function ($h) {
            return strtolower(trim((string) $h));
        }, $headers);

        $idxDestination = array_search('destination', $normalized, true);
        $idxAmount = array_search('amount', $normalized, true);
        $idxAsset = array_search('asset', $normalized, true);
        $idxChain = array_search('chain', $normalized, true);
        $idxNetwork = array_search('network', $normalized, true);

        if ($idxDestination === false || $idxAmount === false) {
            fclose($handle);
            $notify[] = ['error', 'CSV must include destination and amount columns'];
            return back()->withNotify($notify);
        }

        $items = [];
        while (($row = fgetcsv($handle)) !== false) {
            $destination = trim((string) ($row[$idxDestination] ?? ''));
            $amount = (float) ($row[$idxAmount] ?? 0);
            if ($destination === '' || $amount <= 0) {
                continue;
            }

            $itemChain = strtolower((string) ($row[$idxChain] ?? $row[$idxNetwork] ?? $request->default_chain ?? 'tron'));
            $itemChain = $itemChain === 'bep20' ? 'bsc' : $itemChain;
            $itemChain = $itemChain === 'ethereum' ? 'eth' : $itemChain;

            $items[] = [
                'destination' => $destination,
                'amount' => $amount,
                'asset' => strtoupper((string) ($row[$idxAsset] ?? $request->default_asset ?? 'USDT')),
                'chain' => $itemChain,
                'network' => $itemChain,
            ];
        }
        fclose($handle);

        if (empty($items)) {
            $notify[] = ['error', 'No valid rows found in CSV'];
            return back()->withNotify($notify);
        }

        $batch = $this->payoutService->createBatch(auth()->user(), [
            'source' => 'csv',
            'items' => $items,
        ]);

        $notify[] = ['success', 'Mass payout batch created: ' . $batch->reference];
        return back()->withNotify($notify);
    }

    public function reports(Request $request)
    {
        $pageTitle = 'Reports';
        $from = $request->filled('from') ? date('Y-m-d 00:00:00', strtotime($request->from)) : now()->subDays(30)->startOfDay()->toDateTimeString();
        $to = $request->filled('to') ? date('Y-m-d 23:59:59', strtotime($request->to)) : now()->endOfDay()->toDateTimeString();

        $userId = auth()->id();
        $incoming = (float) Transaction::where('user_id', $userId)->where('trx_type', '+')->whereBetween('created_at', [$from, $to])->sum('amount');
        $outgoing = (float) Transaction::where('user_id', $userId)->where('trx_type', '-')->whereBetween('created_at', [$from, $to])->sum('amount');
        $invoiceVolume = (float) Invoice::where('user_id', $userId)->whereBetween('created_at', [$from, $to])->sum('amount');
        $payoutVolume = (float) Payout::where('user_id', $userId)->whereBetween('created_at', [$from, $to])->sum('amount');

        $report = [
            'incoming' => $incoming,
            'outgoing' => $outgoing,
            'net' => $incoming - $outgoing,
            'invoice_volume' => $invoiceVolume,
            'payout_volume' => $payoutVolume,
            'paid_invoices' => Invoice::where('user_id', $userId)->where('status', 'paid')->whereBetween('created_at', [$from, $to])->count(),
            'completed_payouts' => Payout::where('user_id', $userId)->whereIn('status', ['confirmed', 'completed'])->whereBetween('created_at', [$from, $to])->count(),
        ];

        if ($request->get('download') === 'csv') {
            $filename = 'merchant-report-' . date('Ymd-His') . '.csv';
            $rows = [
                ['metric', 'value'],
                ['incoming', $report['incoming']],
                ['outgoing', $report['outgoing']],
                ['net', $report['net']],
                ['invoice_volume', $report['invoice_volume']],
                ['payout_volume', $report['payout_volume']],
                ['paid_invoices', $report['paid_invoices']],
                ['completed_payouts', $report['completed_payouts']],
            ];

            return response()->streamDownload(function () use ($rows) {
                $output = fopen('php://output', 'w');
                foreach ($rows as $row) {
                    fputcsv($output, $row);
                }
                fclose($output);
            }, $filename, ['Content-Type' => 'text/csv']);
        }

        return view(activeTemplate() . 'user.merchant.reports', compact('pageTitle', 'report'));
    }

    public function integration()
    {
        $pageTitle = 'Integration';
        $userId = auth()->id();

        $metrics = [
            'api_keys' => MerchantApiKey::where('user_id', $userId)->count(),
            'api_logs' => ApiRequestLog::where('user_id', $userId)->count(),
            'webhook_deliveries' => WebhookDelivery::whereIn('webhook_endpoint_id', function ($query) use ($userId) {
                $query->select('id')->from('webhook_endpoints')->where('user_id', $userId);
            })->count(),
            'payment_links' => Invoice::where('user_id', $userId)->count(),
        ];

        return view(activeTemplate() . 'user.merchant.integration', compact('pageTitle', 'metrics'));
    }
}

