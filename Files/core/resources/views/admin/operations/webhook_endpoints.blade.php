@extends('admin.layouts.app')

@section('panel')
    <div class="card b-radius--10">
        <div class="card-body p-0">
            <div class="table-responsive--md table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('Merchant ID')</th>
                            <th>@lang('Name')</th>
                            <th>@lang('URL')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Last Rotated')</th>
                            <th>@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($endpoints as $endpoint)
                            <tr>
                                <td>#{{ $endpoint->user_id }}</td>
                                <td>{{ $endpoint->name }}</td>
                                <td class="text-break">{{ $endpoint->url }}</td>
                                <td>
                                    @if($endpoint->status)
                                        <span class="badge badge--success">@lang('Active')</span>
                                    @else
                                        <span class="badge badge--danger">@lang('Disabled')</span>
                                    @endif
                                </td>
                                <td>{{ $endpoint->last_rotated_at ? showDateTime($endpoint->last_rotated_at) : '-' }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <form method="POST" action="{{ route('admin.operations.webhook.endpoints.status', $endpoint->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn--warning btn--sm">
                                                {{ $endpoint->status ? __('Disable') : __('Enable') }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.operations.webhook.endpoints.rotate', $endpoint->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn--primary btn--sm">@lang('Rotate Secret')</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($endpoints->hasPages())
            <div class="card-footer py-4">{{ paginateLinks($endpoints) }}</div>
        @endif
    </div>
@endsection
