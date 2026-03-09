<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ApiRequestLog;
use App\Models\Invoice;
use App\Models\Payout;
use App\Models\WebhookDelivery;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function invoices()
    {
        $pageTitle = 'Invoice Links';
        $invoices = Invoice::where('user_id', auth()->id())->latest()->paginate(getPaginate());
        return view(activeTemplate() . 'user.finance.invoices', compact('pageTitle', 'invoices'));
    }

    public function payouts()
    {
        $pageTitle = 'Payouts';
        $payouts = Payout::where('user_id', auth()->id())->latest()->paginate(getPaginate());
        return view(activeTemplate() . 'user.finance.payouts', compact('pageTitle', 'payouts'));
    }

    public function apiLogs()
    {
        $pageTitle = 'API Logs';
        $logs = ApiRequestLog::where('user_id', auth()->id())->latest()->paginate(getPaginate());
        return view(activeTemplate() . 'user.finance.api_logs', compact('pageTitle', 'logs'));
    }

    public function webhookLogs()
    {
        $pageTitle = 'Webhook Deliveries';
        $logs = WebhookDelivery::query()
            ->whereIn('webhook_endpoint_id', function ($query) {
                $query->select('id')->from('webhook_endpoints')->where('user_id', auth()->id());
            })
            ->latest()
            ->paginate(getPaginate());
        return view(activeTemplate() . 'user.finance.webhook_logs', compact('pageTitle', 'logs'));
    }
}
