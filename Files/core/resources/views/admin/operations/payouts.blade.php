@extends('admin.layouts.app')

@section('panel')
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <div>
            <span class="badge {{ $payoutsFrozen ? 'badge--danger' : 'badge--success' }}">
                {{ $payoutsFrozen ? __('Payouts are currently frozen') : __('Payouts are currently active') }}
            </span>
        </div>
        <form method="POST" action="{{ route('admin.operations.payouts.freeze.toggle') }}">
            @csrf
            <button type="submit" class="btn btn--sm {{ $payoutsFrozen ? 'btn--success' : 'btn--danger' }}">
                {{ $payoutsFrozen ? __('Resume Payouts') : __('Freeze Payouts') }}
            </button>
        </form>
    </div>
    <div class="card b-radius--10">
        <div class="card-body p-0">
            <div class="table-responsive--md table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('Reference')</th>
                            <th>@lang('Merchant')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Asset')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payouts as $payout)
                            <tr>
                                <td>{{ $payout->reference }}</td>
                                <td>{{ $payout->user_id }}</td>
                                <td>{{ $payout->status }}</td>
                                <td>{{ $payout->asset }}</td>
                                <td>{{ showAmount($payout->amount, 8, true, false, false) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.operations.payouts.status', $payout->id) }}" class="d-grid gap-1">
                                        @csrf
                                        <select name="status" class="form-control form-control-sm">
                                            @foreach(['pending','queued','processing','completed','failed','cancelled','rejected'] as $status)
                                                <option value="{{ $status }}" @selected($payout->status === $status)>{{ ucfirst($status) }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="failure_reason" class="form-control form-control-sm" placeholder="@lang('Reason (optional)')">
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
        @if($payouts->hasPages())
            <div class="card-footer py-4">{{ paginateLinks($payouts) }}</div>
        @endif
    </div>
@endsection
