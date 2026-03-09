@extends('admin.layouts.app')

@section('panel')
    <div class="card b-radius--10">
        <div class="card-body p-0">
            <div class="table-responsive--md table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('Request ID')</th>
                            <th>@lang('Method')</th>
                            <th>@lang('Endpoint')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Duration')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>{{ $log->request_id }}</td>
                                <td>{{ $log->method }}</td>
                                <td>{{ $log->endpoint }}</td>
                                <td>{{ $log->status_code }}</td>
                                <td>{{ $log->duration_ms }} ms</td>
                            </tr>
                        @empty
                            <tr><td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
            <div class="card-footer py-4">{{ paginateLinks($logs) }}</div>
        @endif
    </div>
@endsection
