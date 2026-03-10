@extends($activeTemplate . 'layouts.master')

@php
    $request = request();
@endphp

@section('content')
    <div class="row gy-3">
        <div class="col-12">
            <h3 class="mb-1">{{ __($pageTitle) }}</h3>
            <p class="mb-0">@lang('Filter by source, chain, status, and transaction/address reference.')</p>
        </div>

        <div class="col-12">
            <div class="card custom--card">
                <div class="card-body">
                    <form class="row g-2">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" value="{{ $request->search }}"
                                placeholder="@lang('Reference / Tx hash / Address')">
                        </div>
                        <div class="col-md-2">
                            <select name="source" class="form-control">
                                <option value="">@lang('All Sources')</option>
                                <option value="deposit" @selected($request->source === 'deposit')>@lang('Deposits')</option>
                                <option value="payout" @selected($request->source === 'payout')>@lang('Payouts')</option>
                                <option value="invoice" @selected($request->source === 'invoice')>@lang('Invoices')</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="chain" class="form-control">
                                <option value="">@lang('All Chains')</option>
                                <option value="tron" @selected($request->chain === 'tron')>TRON</option>
                                <option value="eth" @selected($request->chain === 'eth')>ETH</option>
                                <option value="bsc" @selected($request->chain === 'bsc')>BSC</option>
                                <option value="ton" @selected($request->chain === 'ton')>TON</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input name="date" type="search" class="form-control date-range"
                                placeholder="@lang('Start Date - End Date')" autocomplete="off" value="{{ $request->date }}">
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="status" class="form-control" value="{{ $request->status }}"
                                placeholder="@lang('Status')">
                        </div>
                        <div class="col-md-1 d-grid">
                            <button class="btn btn--base" type="submit">@lang('Go')</button>
                        </div>
                    </form>
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
                                    <th>@lang('Date')</th>
                                    <th>@lang('Source')</th>
                                    <th>@lang('Reference')</th>
                                    <th>@lang('Chain')</th>
                                    <th>@lang('Asset')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Address')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $tx)
                                    <tr>
                                        <td>{{ showDateTime($tx['created_at']) }}</td>
                                        <td>{{ ucfirst($tx['source']) }}</td>
                                        <td class="text-break">{{ $tx['reference'] }}</td>
                                        <td>{{ $tx['chain'] }}</td>
                                        <td>{{ $tx['asset'] }}</td>
                                        <td class="{{ $tx['amount'] >= 0 ? 'text--success' : 'text--danger' }}">
                                            {{ showAmount($tx['amount'], 8, true, false, false) }}
                                        </td>
                                        <td>{{ $tx['status'] }}</td>
                                        <td class="text-break">{{ $tx['address'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($transactions->hasPages())
                        <div class="p-3">{{ paginateLinks($transactions) }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';
            const datePicker = $('.date-range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                },
                showDropdowns: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                },
                maxDate: moment()
            });

            $('.date-range').on('apply.daterangepicker', function(event, picker) {
                $(event.target).val(picker.startDate.format('MMMM DD, YYYY') + ' - ' + picker.endDate.format('MMMM DD, YYYY'));
            });
        })(jQuery);
    </script>
@endpush

