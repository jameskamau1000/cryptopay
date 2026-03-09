@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="row gy-3">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h3 class="mb-1">{{ __($pageTitle) }}</h3>
                    <p class="mb-0">@lang('Generate summary reports for transactions, invoices, and payouts.')</p>
                </div>
                <div>
                    <a href="{{ route('user.merchant.reports', array_merge(request()->query(), ['download' => 'csv'])) }}"
                        class="btn btn--base">@lang('Download CSV')</a>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card custom--card">
                <div class="card-body">
                    <form class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">@lang('From')</label>
                            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">@lang('To')</label>
                            <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn--base w-100" type="submit">@lang('Generate')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card custom--card h-100">
                <div class="card-body">
                    <h6 class="mb-1">@lang('Incoming')</h6>
                    <h5 class="mb-0">{{ showAmount($report['incoming'], 8, true, false, false) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card custom--card h-100">
                <div class="card-body">
                    <h6 class="mb-1">@lang('Outgoing')</h6>
                    <h5 class="mb-0">{{ showAmount($report['outgoing'], 8, true, false, false) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card custom--card h-100">
                <div class="card-body">
                    <h6 class="mb-1">@lang('Net')</h6>
                    <h5 class="mb-0">{{ showAmount($report['net'], 8, true, false, false) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card custom--card h-100">
                <div class="card-body">
                    <h6 class="mb-1">@lang('Invoice Volume')</h6>
                    <h5 class="mb-0">{{ showAmount($report['invoice_volume'], 8, true, false, false) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card custom--card h-100">
                <div class="card-body">
                    <h6 class="mb-1">@lang('Payout Volume')</h6>
                    <h5 class="mb-0">{{ showAmount($report['payout_volume'], 8, true, false, false) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card custom--card h-100">
                <div class="card-body">
                    <h6 class="mb-1">@lang('Completed Counts')</h6>
                    <div class="mb-0">@lang('Paid Invoices'): {{ $report['paid_invoices'] }}</div>
                    <div class="mb-0">@lang('Completed Payouts'): {{ $report['completed_payouts'] }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection

