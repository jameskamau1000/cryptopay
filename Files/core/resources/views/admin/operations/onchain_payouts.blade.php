@extends('admin.layouts.app')

@section('panel')
    <div class="card b-radius--10">
        <div class="card-body p-0">
            <div class="table-responsive--md table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('Payout')</th>
                            <th>@lang('Tx Hash')</th>
                            <th>@lang('Chain')</th>
                            <th>@lang('Asset')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('To')</th>
                            <th>@lang('Status')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payouts as $item)
                            <tr>
                                <td>{{ optional($item->payout)->reference ?: $item->payout_id }}</td>
                                <td class="text-break">{{ $item->tx_hash ?: '-' }}</td>
                                <td>{{ strtoupper($item->chain) }}</td>
                                <td>{{ strtoupper($item->asset) }}</td>
                                <td>{{ showAmount($item->amount, 8, true, false, false) }}</td>
                                <td class="text-break">{{ $item->to_address }}</td>
                                <td>{{ ucfirst($item->status) }}</td>
                            </tr>
                        @empty
                            <tr><td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($payouts->hasPages())
            <div class="card-footer py-4">{{ paginateLinks($payouts) }}</div>
        @endif
    </div>
@endsection
