@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="row gy-3">
        <div class="col-12">
            <h3 class="mb-1">{{ __($pageTitle) }}</h3>
            <p class="mb-0">@lang('API and webhook integration controls for your merchant workspace.')</p>
        </div>

        <div class="col-md-3">
            <div class="card custom--card h-100">
                <div class="card-body">
                    <h6 class="mb-1">@lang('API Keys')</h6>
                    <h4 class="mb-0">{{ $metrics['api_keys'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card custom--card h-100">
                <div class="card-body">
                    <h6 class="mb-1">@lang('API Logs')</h6>
                    <h4 class="mb-0">{{ $metrics['api_logs'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card custom--card h-100">
                <div class="card-body">
                    <h6 class="mb-1">@lang('Webhook Deliveries')</h6>
                    <h4 class="mb-0">{{ $metrics['webhook_deliveries'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card custom--card h-100">
                <div class="card-body">
                    <h6 class="mb-1">@lang('Payment Links')</h6>
                    <h4 class="mb-0">{{ $metrics['payment_links'] }}</h4>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card custom--card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('user.api.key') }}" class="btn btn-outline--base w-100">@lang('API Keys')</a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('user.finance.api.logs') }}" class="btn btn-outline--base w-100">@lang('API Logs')</a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('user.finance.webhook.logs') }}" class="btn btn-outline--base w-100">@lang('Webhook Logs')</a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('developer.documentation') }}" class="btn btn-outline--base w-100">@lang('Developer Docs')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

