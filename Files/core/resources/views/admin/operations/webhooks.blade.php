@extends('admin.layouts.app')

@section('panel')
    <div class="card b-radius--10">
        <div class="card-body p-0">
            <div class="table-responsive--md table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('Delivery ID')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Attempts')</th>
                            <th>@lang('HTTP')</th>
                            <th>@lang('Updated')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deliveries as $delivery)
                            <tr>
                                <td>#{{ $delivery->id }}</td>
                                <td>{{ $delivery->status }}</td>
                                <td>{{ $delivery->attempts }}</td>
                                <td>{{ $delivery->response_code ?? '-' }}</td>
                                <td>{{ showDateTime($delivery->updated_at) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.operations.webhooks.retry', $delivery->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn--warning btn--sm">@lang('Retry')</button>
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
        @if($deliveries->hasPages())
            <div class="card-footer py-4">{{ paginateLinks($deliveries) }}</div>
        @endif
    </div>
@endsection
