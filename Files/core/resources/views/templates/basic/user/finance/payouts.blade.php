@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="row">
        <div class="col-12 mb-3">
            <h4>{{ __($pageTitle) }}</h4>
        </div>
        <div class="col-12">
            <div class="card custom--card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table--responsive--md">
                            <thead>
                                <tr>
                                    <th>@lang('Reference')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Asset')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Destination')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payouts as $payout)
                                    <tr>
                                        <td>{{ $payout->reference }}</td>
                                        <td><span class="badge badge--warning">{{ $payout->status }}</span></td>
                                        <td>{{ $payout->asset }}</td>
                                        <td>{{ showAmount($payout->amount, 8, true, false, false) }}</td>
                                        <td class="text-break">{{ $payout->destination }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($payouts->hasPages())
                        <div class="p-3">{{ paginateLinks($payouts) }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
