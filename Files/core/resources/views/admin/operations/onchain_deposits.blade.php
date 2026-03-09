@extends('admin.layouts.app')

@section('panel')
    <div class="card b-radius--10">
        <div class="card-body p-0">
            <div class="table-responsive--md table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('Tx Hash')</th>
                            <th>@lang('Merchant')</th>
                            <th>@lang('Invoice')</th>
                            <th>@lang('Chain')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Confirmations')</th>
                            <th>@lang('Status')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deposits as $deposit)
                            <tr>
                                <td class="text-break">{{ $deposit->tx_hash }}</td>
                                <td>{{ optional($deposit->user)->username ?: $deposit->user_id }}</td>
                                <td>{{ optional($deposit->invoice)->reference ?: $deposit->invoice_id }}</td>
                                <td>{{ strtoupper($deposit->chain) }}</td>
                                <td>{{ showAmount($deposit->amount, 8, true, false, false) }} {{ strtoupper($deposit->asset) }}</td>
                                <td>{{ $deposit->confirmations }}</td>
                                <td>{{ ucfirst($deposit->status) }}</td>
                            </tr>
                        @empty
                            <tr><td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($deposits->hasPages())
            <div class="card-footer py-4">{{ paginateLinks($deposits) }}</div>
        @endif
    </div>
@endsection
