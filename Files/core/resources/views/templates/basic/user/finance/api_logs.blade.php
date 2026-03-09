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
                                    <th>@lang('Request Id')</th>
                                    <th>@lang('Endpoint')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Duration')</th>
                                    <th>@lang('At')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->request_id }}</td>
                                        <td>{{ $log->method }} {{ $log->endpoint }}</td>
                                        <td>{{ $log->status_code }}</td>
                                        <td>{{ $log->duration_ms }} ms</td>
                                        <td>{{ showDateTime($log->created_at) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($logs->hasPages())
                        <div class="p-3">{{ paginateLinks($logs) }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
