@extends('admin.layouts.app')

@section('panel')
    <div class="card b-radius--10">
        <div class="card-body p-0">
            <div class="table-responsive--md table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('Merchant')</th>
                            <th>@lang('Invoice')</th>
                            <th>@lang('Chain')</th>
                            <th>@lang('Asset')</th>
                            <th>@lang('Address')</th>
                            <th>@lang('Status')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($addresses as $address)
                            <tr>
                                <td>{{ optional($address->user)->username ?: $address->user_id }}</td>
                                <td>{{ optional($address->invoice)->reference ?: $address->invoice_id }}</td>
                                <td>{{ strtoupper($address->chain) }}</td>
                                <td>{{ strtoupper($address->asset) }}</td>
                                <td class="text-break">{{ $address->address }}</td>
                                <td>{{ ucfirst($address->status) }}</td>
                            </tr>
                        @empty
                            <tr><td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($addresses->hasPages())
            <div class="card-footer py-4">{{ paginateLinks($addresses) }}</div>
        @endif
    </div>
@endsection
