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
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Currency')</th>
                                    <th>@lang('Created')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->reference }}</td>
                                        <td><span class="badge badge--info">{{ $invoice->status }}</span></td>
                                        <td>{{ showAmount($invoice->amount, 8, true, false, false) }}</td>
                                        <td>{{ $invoice->currency }}</td>
                                        <td>{{ showDateTime($invoice->created_at) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($invoices->hasPages())
                        <div class="p-3">{{ paginateLinks($invoices) }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
