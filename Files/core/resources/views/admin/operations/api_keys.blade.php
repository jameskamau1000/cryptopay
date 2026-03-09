@extends('admin.layouts.app')

@section('panel')
    <div class="card b-radius--10">
        <div class="card-body p-0">
            <div class="table-responsive--md table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('Merchant')</th>
                            <th>@lang('Name')</th>
                            <th>@lang('Public Key')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Last Used')</th>
                            <th>@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($keys as $key)
                            <tr>
                                <td>{{ $key->user?->username ?? ('#'.$key->user_id) }}</td>
                                <td>{{ $key->name }}</td>
                                <td><code>{{ $key->public_key }}</code></td>
                                <td>
                                    @if($key->status)
                                        <span class="badge badge--success">@lang('Active')</span>
                                    @else
                                        <span class="badge badge--danger">@lang('Disabled')</span>
                                    @endif
                                </td>
                                <td>{{ $key->last_used_at ? showDateTime($key->last_used_at) : '-' }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <form method="POST" action="{{ route('admin.operations.api.keys.status', $key->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn--warning btn--sm">
                                                {{ $key->status ? __('Disable') : __('Enable') }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.operations.api.keys.regenerate', $key->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn--primary btn--sm">@lang('Regenerate Secret')</button>
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
        @if($keys->hasPages())
            <div class="card-footer py-4">{{ paginateLinks($keys) }}</div>
        @endif
    </div>
@endsection
