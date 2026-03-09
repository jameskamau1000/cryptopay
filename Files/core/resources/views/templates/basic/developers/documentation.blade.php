@extends($activeTemplate . 'layouts.frontend')

@section('content')
<section class="py-120">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3">
                <div class="card custom--card p-3 sticky-top" style="top: 100px;">
                    <h6 class="mb-3">@lang('New Developer Docs')</h6>
                    <ul class="list-unstyled mb-0 d-grid gap-2">
                        <li><a href="#welcome">@lang('Welcome')</a></li>
                        <li><a href="#environments">@lang('Environments')</a></li>
                        <li><a href="#credentials">@lang('API Credentials')</a></li>
                        <li><a href="#auth">@lang('Authentication')</a></li>
                        <li><a href="#idempotency">@lang('Idempotency')</a></li>
                        <li><a href="#invoices">@lang('Invoices')</a></li>
                        <li><a href="#payouts">@lang('Payouts')</a></li>
                        <li><a href="#webhooks">@lang('Webhooks')</a></li>
                        <li><a href="#currencies">@lang('Currencies')</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card custom--card p-4">
                    <div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
                        <a href="{{ route('api.documentation') }}" class="btn btn--dark btn--sm">@lang('Open Classic Guide')</a>
                        <a href="{{ route('api.reference') }}" class="btn btn--base btn--sm">@lang('API Reference')</a>
                    </div>

                    <section id="welcome" class="mb-5">
                        <h3 class="mb-2">@lang('Welcome')</h3>
                        <p>@lang('CryptoPay helps you accept crypto payments, create invoice links, run payouts, and receive signed webhook events.')</p>
                    </section>

                    <section id="environments" class="mb-5">
                        <h4 class="mb-2">@lang('Environments')</h4>
                        <p class="mb-2">@lang('Base URL')</p>
                        <pre class="bg-dark text-white p-3 rounded"><code>{{ $baseUrl }}/api/v1</code></pre>
                    </section>

                    <section id="credentials" class="mb-5">
                        <h4 class="mb-2">@lang('API Credentials')</h4>
                        <p>@lang('Create API keys from merchant dashboard, then use the public key in headers and secret key for HMAC signing.')</p>
                    </section>

                    <section id="auth" class="mb-5">
                        <h4 class="mb-2">@lang('Authentication')</h4>
                        <p>@lang('Every request must include these headers:')</p>
                        <ul>
                            <li><code>X-API-KEY</code></li>
                            <li><code>X-API-SIGNATURE</code></li>
                            <li><code>X-API-TIMESTAMP</code></li>
                            <li><code>X-API-NONCE</code></li>
                        </ul>
                        <pre class="bg-dark text-white p-3 rounded"><code>signature_payload = timestamp + "." + nonce + "." + sha256(raw_body)
signature = HMAC_SHA256(signature_payload, secret_key)</code></pre>
                    </section>

                    <section id="idempotency" class="mb-5">
                        <h4 class="mb-2">@lang('Idempotency')</h4>
                        <p>@lang('For POST endpoints, send') <code>Idempotency-Key</code> @lang('to safely retry requests without duplicate resources.')</p>
                    </section>

                    <section id="invoices" class="mb-5">
                        <h4 class="mb-2">@lang('Invoices')</h4>
                        <ul>
                            <li><code>POST /api/v1/invoices</code> - @lang('Create invoice')</li>
                            <li><code>GET /api/v1/invoices</code> - @lang('List invoices')</li>
                            <li><code>GET /api/v1/invoices/{id}</code> - @lang('Get invoice')</li>
                        </ul>
                    </section>

                    <section id="payouts" class="mb-5">
                        <h4 class="mb-2">@lang('Payouts')</h4>
                        <ul>
                            <li><code>POST /api/v1/payouts</code> - @lang('Create single payout')</li>
                            <li><code>POST /api/v1/payouts/batch</code> - @lang('Create batch payout')</li>
                            <li><code>POST /api/v1/payouts/batch/csv</code> - @lang('Upload CSV batch')</li>
                            <li><code>GET /api/v1/payouts/{id}</code> - @lang('Get payout')</li>
                        </ul>
                    </section>

                    <section id="webhooks" class="mb-5">
                        <h4 class="mb-2">@lang('Webhooks')</h4>
                        <ul>
                            <li><code>POST /api/v1/webhooks</code> - @lang('Register endpoint')</li>
                            <li><code>POST /api/v1/webhooks/{id}/rotate-secret</code> - @lang('Rotate secret')</li>
                            <li><code>POST /api/v1/webhooks/test</code> - @lang('Send test event')</li>
                        </ul>
                        <p>@lang('Verify webhook signature from header') <code>X-CryptoPay-Signature</code>.</p>
                    </section>

                    <section id="currencies">
                        <h4 class="mb-2">@lang('Supported Currencies')</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr><th>@lang('Currency')</th><th>@lang('Symbol')</th></tr>
                                </thead>
                                <tbody>
                                    @forelse($allCurrency as $currency)
                                        <tr>
                                            <td>{{ $currency->currency }}</td>
                                            <td>{{ $currency->symbol }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="2">@lang('No active currencies configured yet.')</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
