@extends('admin.layouts.app')

@section('panel')
    <div class="card b-radius--10">
        <div class="card-body p-0">
            <div class="table-responsive--md table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('Reference')</th>
                            <th>@lang('Merchant')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Created')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->reference }}</td>
                                <td>{{ $invoice->user_id }}</td>
                                <td>{{ $invoice->status }}</td>
                                <td>{{ showAmount($invoice->amount, 8, true, false, false) }} {{ $invoice->currency }}</td>
                                <td>{{ showDateTime($invoice->created_at) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.operations.invoices.status', $invoice->id) }}" class="d-flex gap-2">
                                        @csrf
                                        <select name="status" class="form-control form-control-sm">
                                            @foreach(['draft','created','pending','paid','expired','cancelled','rejected','underpaid','overpaid'] as $status)
                                                <option value="{{ $status }}" @selected($invoice->status === $status)>{{ ucfirst($status) }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn--primary btn--sm">@lang('Save')</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($invoices->hasPages())
            <div class="card-footer py-4">{{ paginateLinks($invoices) }}</div>
        @endif
    </div>
@endsection
