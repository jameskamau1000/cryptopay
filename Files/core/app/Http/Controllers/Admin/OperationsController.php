<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiRequestLog;
use App\Models\CustodyWallet;
use App\Models\Invoice;
use App\Models\MerchantApiKey;
use App\Models\MerchantDepositAddress;
use App\Models\OnchainDeposit;
use App\Models\OnchainPayout;
use App\Models\Payout;
use App\Models\RiskCase;
use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use App\Jobs\SendWebhookDeliveryJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class OperationsController extends Controller
{
    public function invoices()
    {
        $pageTitle = 'CryptoPay Operations - Invoices';
        $invoices = Invoice::latest()->paginate(getPaginate());
        return view('admin.operations.invoices', compact('pageTitle', 'invoices'));
    }

    public function payouts()
    {
        $pageTitle = 'CryptoPay Operations - Payouts';
        $payouts = Payout::latest()->paginate(getPaginate());
        $payoutsFrozen = (bool) Cache::get('cryptopay:payouts:frozen', config('operations.payouts_frozen', false));
        return view('admin.operations.payouts', compact('pageTitle', 'payouts', 'payoutsFrozen'));
    }

    public function payoutFreezeToggle()
    {
        $current = (bool) Cache::get('cryptopay:payouts:frozen', config('operations.payouts_frozen', false));
        Cache::forever('cryptopay:payouts:frozen', !$current);

        $notify[] = ['success', !$current ? 'Payouts frozen successfully' : 'Payouts resumed successfully'];
        return back()->withNotify($notify);
    }

    public function apiKeys()
    {
        $pageTitle = 'CryptoPay Operations - API Keys';
        $keys = MerchantApiKey::with('user')->latest()->paginate(getPaginate());
        return view('admin.operations.api_keys', compact('pageTitle', 'keys'));
    }

    public function webhookEndpoints()
    {
        $pageTitle = 'CryptoPay Operations - Webhook Endpoints';
        $endpoints = WebhookEndpoint::latest()->paginate(getPaginate());
        return view('admin.operations.webhook_endpoints', compact('pageTitle', 'endpoints'));
    }

    public function webhookDeliveries()
    {
        $pageTitle = 'CryptoPay Operations - Webhook Deliveries';
        $deliveries = WebhookDelivery::latest()->paginate(getPaginate());
        return view('admin.operations.webhooks', compact('pageTitle', 'deliveries'));
    }

    public function apiLogs()
    {
        $pageTitle = 'CryptoPay Operations - API Request Logs';
        $logs = ApiRequestLog::latest()->paginate(getPaginate());
        return view('admin.operations.api_logs', compact('pageTitle', 'logs'));
    }

    public function riskCases()
    {
        $pageTitle = 'CryptoPay Operations - Risk Cases';
        $cases = RiskCase::latest()->paginate(getPaginate());
        return view('admin.operations.risk_cases', compact('pageTitle', 'cases'));
    }

    public function wallets()
    {
        $pageTitle = 'CryptoPay Operations - Treasury Wallets';
        $wallets = CustodyWallet::latest()->paginate(getPaginate());
        return view('admin.operations.wallets', compact('pageTitle', 'wallets'));
    }

    public function walletStore(Request $request)
    {
        $request->validate([
            'chain' => 'required|in:tron,eth,bsc,bep20,ton',
            'asset' => 'required|string|max:20',
            'address' => 'required|string|max:255|unique:custody_wallets,address',
            'label' => 'nullable|string|max:255',
            'private_key' => 'nullable|string|max:5000',
            'is_treasury' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        CustodyWallet::create([
            'chain' => strtolower($request->chain) === 'bep20' ? 'bsc' : strtolower($request->chain),
            'asset' => strtoupper($request->asset),
            'address' => $request->address,
            'label' => $request->label,
            'encrypted_private_key' => $request->filled('private_key') ? Crypt::encryptString($request->private_key) : null,
            'is_treasury' => $request->boolean('is_treasury'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $notify[] = ['success', 'Wallet added successfully'];
        return back()->withNotify($notify);
    }

    public function walletStatus(int $id)
    {
        $wallet = CustodyWallet::findOrFail($id);
        $wallet->is_active = !$wallet->is_active;
        $wallet->save();

        $notify[] = ['success', 'Wallet status updated'];
        return back()->withNotify($notify);
    }

    public function walletVaultUpdate(Request $request, int $id)
    {
        $request->validate([
            'private_key' => 'required|string|max:5000',
        ]);

        $wallet = CustodyWallet::findOrFail($id);
        $wallet->encrypted_private_key = Crypt::encryptString($request->private_key);
        $wallet->save();

        $notify[] = ['success', 'Wallet vault key updated'];
        return back()->withNotify($notify);
    }

    public function walletVaultClear(int $id)
    {
        $wallet = CustodyWallet::findOrFail($id);
        $wallet->encrypted_private_key = null;
        $wallet->save();

        $notify[] = ['success', 'Wallet vault key cleared'];
        return back()->withNotify($notify);
    }

    public function depositAddresses()
    {
        $pageTitle = 'CryptoPay Operations - Deposit Addresses';
        $addresses = MerchantDepositAddress::with('user', 'invoice')->latest()->paginate(getPaginate());
        return view('admin.operations.deposit_addresses', compact('pageTitle', 'addresses'));
    }

    public function onchainDeposits()
    {
        $pageTitle = 'CryptoPay Operations - On-chain Deposits';
        $deposits = OnchainDeposit::with('user', 'invoice')->latest()->paginate(getPaginate());
        return view('admin.operations.onchain_deposits', compact('pageTitle', 'deposits'));
    }

    public function onchainPayouts()
    {
        $pageTitle = 'CryptoPay Operations - On-chain Payouts';
        $payouts = OnchainPayout::with('user', 'payout')->latest()->paginate(getPaginate());
        return view('admin.operations.onchain_payouts', compact('pageTitle', 'payouts'));
    }

    public function invoiceStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:draft,created,pending,paid,expired,cancelled,rejected,underpaid,overpaid',
        ]);

        $invoice = Invoice::findOrFail($id);
        $invoice->status = $request->status;
        if ($request->status === 'paid' && !$invoice->paid_at) {
            $invoice->paid_at = now();
            $invoice->paid_amount = $invoice->amount;
            $invoice->settlement_amount = $invoice->amount;
        }
        $invoice->save();

        $notify[] = ['success', 'Invoice status updated successfully'];
        return back()->withNotify($notify);
    }

    public function payoutStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:pending,queued,processing,completed,failed,cancelled,rejected',
            'failure_reason' => 'nullable|string|max:255',
        ]);

        $payout = Payout::findOrFail($id);
        $payout->status = $request->status;
        $payout->failure_reason = $request->failure_reason;
        if (in_array($request->status, ['completed', 'failed', 'rejected'])) {
            $payout->processed_at = now();
        }
        $payout->save();

        $notify[] = ['success', 'Payout status updated successfully'];
        return back()->withNotify($notify);
    }

    public function retryDelivery(int $id)
    {
        $delivery = WebhookDelivery::findOrFail($id);
        $delivery->status = 'queued';
        $delivery->next_retry_at = now();
        $delivery->save();

        SendWebhookDeliveryJob::dispatch($delivery->id);
        $notify[] = ['success', 'Webhook delivery queued for retry'];
        return back()->withNotify($notify);
    }

    public function endpointStatus(int $id)
    {
        $endpoint = WebhookEndpoint::findOrFail($id);
        $endpoint->status = $endpoint->status ? 0 : 1;
        $endpoint->save();

        $notify[] = ['success', 'Webhook endpoint status updated'];
        return back()->withNotify($notify);
    }

    public function endpointRotateSecret(int $id)
    {
        $endpoint = WebhookEndpoint::findOrFail($id);
        $endpoint->secret = Str::random(64);
        $endpoint->last_rotated_at = now();
        $endpoint->save();

        $notify[] = ['success', 'Endpoint secret rotated'];
        return back()->withNotify($notify);
    }

    public function keyStatus(int $id)
    {
        $key = MerchantApiKey::findOrFail($id);
        $key->status = $key->status ? 0 : 1;
        $key->save();

        $notify[] = ['success', 'API key status updated'];
        return back()->withNotify($notify);
    }

    public function keyRegenerateSecret(int $id)
    {
        $key = MerchantApiKey::findOrFail($id);
        $key->secret_key = 'cps_' . Str::random(60);
        $key->save();

        $notify[] = ['success', 'API key secret regenerated'];
        return back()->withNotify($notify);
    }

    public function riskCaseUpdate(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_review,blocked,resolved,dismissed',
            'severity' => 'required|in:low,medium,high,critical',
        ]);

        $case = RiskCase::findOrFail($id);
        $case->status = $request->status;
        $case->severity = $request->severity;
        $case->assigned_admin_id = auth('admin')->id();
        if (in_array($request->status, ['resolved', 'dismissed'])) {
            $case->resolved_at = now();
        }
        $case->save();

        $notify[] = ['success', 'Risk case updated successfully'];
        return back()->withNotify($notify);
    }
}
