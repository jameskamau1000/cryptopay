@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="row gy-3">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h3 class="mb-1">{{ __($pageTitle) }}</h3>
                    <p class="mb-0">@lang('Create and share payment links with your payers.')</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card custom--card h-100">
                <div class="card-body">
                    <h6 class="mb-1">@lang('Total Links')</h6>
                    <h4 class="mb-0">{{ $stats['total'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card custom--card h-100">
                <div class="card-body">
                    <h6 class="mb-1">@lang('Paid')</h6>
                    <h4 class="mb-0">{{ $stats['paid'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card custom--card h-100">
                <div class="card-body">
                    <h6 class="mb-1">@lang('Pending')</h6>
                    <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card custom--card">
                <div class="card-header">
                    <h6 class="mb-0">@lang('Create payment link')</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user.merchant.payment.links.store') }}" class="row g-3">
                        @csrf
                        <div class="col-md-3">
                            <label class="form-label">@lang('Amount')</label>
                            <input type="number" step="0.00000001" min="0" name="amount" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">@lang('Currency')</label>
                            <input type="text" name="currency" class="form-control" value="USD" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">@lang('Settlement')</label>
                            <input type="text" name="settlement_currency" class="form-control" value="USDT">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">@lang('Chain')</label>
                            <select name="chain" class="form-control" required>
                                <option value="tron">TRON</option>
                                <option value="eth">ETH</option>
                                <option value="bsc">BSC</option>
                                <option value="ton">TON</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">@lang('Reference (optional)')</label>
                            <input type="text" name="reference" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">@lang('Type')</label>
                            <select name="type" class="form-control" required>
                                <option value="one_time">@lang('One-time')</option>
                                <option value="reusable">@lang('Reusable')</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">@lang('Asset')</label>
                            <input type="text" name="asset" class="form-control" value="USDT">
                        </div>
                        <div class="col-12">
                            <button class="btn btn--base" type="submit">@lang('Create payment link')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card custom--card">
                <div class="card-header">
                    <h6 class="mb-0">@lang('Payment links')</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table--responsive--md">
                            <thead>
                                <tr>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Reference')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Currency')</th>
                                    <th>@lang('Chain')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Checkout URL')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                    <tr>
                                        <td>{{ showDateTime($invoice->created_at) }}</td>
                                        <td>{{ $invoice->reference }}</td>
                                        <td>{{ showAmount($invoice->amount, 8, true, false, false) }}</td>
                                        <td>{{ $invoice->currency }}</td>
                                        <td>{{ strtoupper($invoice->depositAddress->chain ?? '-') }}</td>
                                        <td><span class="badge badge--info">{{ $invoice->status }}</span></td>
                                        <td class="text-break">{{ $invoice->checkout_url ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($invoices->hasPages())
                        <div class="p-3">{{ paginateLinks($invoices) }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

