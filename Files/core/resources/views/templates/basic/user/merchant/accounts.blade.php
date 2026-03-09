@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="row gy-3">
        <div class="col-12">
            <h3 class="mb-1">{{ __($pageTitle) }}</h3>
            <p class="mb-0">@lang('Overview of balances by asset using your on-chain deposits and payouts.')</p>
        </div>

        <div class="col-12">
            <div class="card custom--card">
                <div class="card-body">
                    <h6 class="mb-1">@lang('Total Net Balance')</h6>
                    <h3 class="mb-0">{{ showAmount($totalNet, 8, true, false, false) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card custom--card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table--responsive--md">
                            <thead>
                                <tr>
                                    <th>@lang('Asset')</th>
                                    <th>@lang('Received')</th>
                                    <th>@lang('Sent')</th>
                                    <th>@lang('Net')</th>
                                    <th>@lang('Channels')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($accounts as $account)
                                    <tr>
                                        <td>{{ $account['asset'] }}</td>
                                        <td>{{ showAmount($account['incoming'], 8, true, false, false) }}</td>
                                        <td>{{ showAmount($account['outgoing'], 8, true, false, false) }}</td>
                                        <td>{{ showAmount($account['net'], 8, true, false, false) }}</td>
                                        <td>{{ $account['channels'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

