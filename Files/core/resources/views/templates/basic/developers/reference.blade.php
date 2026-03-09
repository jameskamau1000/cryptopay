@extends($activeTemplate . 'layouts.frontend')

@section('content')
<section class="py-120">
    <div class="container">
        <div class="card custom--card p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                <h3 class="mb-0">@lang('CryptoPay API Reference')</h3>
                <a href="{{ route('api.documentation') }}" class="btn btn--base btn--sm">@lang('Back to Docs')</a>
            </div>

            <p class="mb-4"><strong>@lang('Base URL:')</strong> <code>{{ $baseUrl }}/api/v1</code></p>

            <h5 class="mb-2">@lang('Create Invoice')</h5>
            <pre class="bg-dark text-white p-3 rounded"><code>POST /api/v1/invoices
{
  "reference": "INV-1001",
  "currency": "USD",
  "amount": 125.50,
  "chain": "tron",
  "asset": "USDT",
  "customer": {"name": "John Doe", "email": "john@example.com"}
}</code></pre>

            <h5 class="mt-4 mb-2">@lang('Get Invoice')</h5>
            <pre class="bg-dark text-white p-3 rounded"><code>GET /api/v1/invoices/INV-1001</code></pre>

            <h5 class="mt-4 mb-2">@lang('Create Payout')</h5>
            <pre class="bg-dark text-white p-3 rounded"><code>POST /api/v1/payouts
{
  "amount": 50,
  "asset": "USDT",
  "chain": "bsc",
  "destination": "0xRecipientAddress",
  "metadata": {
    "signed_raw_tx": "0x...signed_transaction_hex..."
  }
}</code></pre>

            <h5 class="mt-4 mb-2">@lang('Create Batch Payout')</h5>
            <pre class="bg-dark text-white p-3 rounded"><code>POST /api/v1/payouts/batch
{
  "items": [
    {"destination": "TXXXX1", "amount": 10, "asset": "USDT", "chain": "tron"},
    {"destination": "0xabc...123", "amount": 20, "asset": "USDT", "chain": "eth"},
    {"destination": "0xbsc...789", "amount": 25, "asset": "USDT", "chain": "bsc"},
    {"destination": "UQabc...xyz", "amount": 30, "asset": "USDT", "chain": "ton"}
  ]
}</code></pre>

            <h5 class="mt-4 mb-2">@lang('Webhook Test')</h5>
            <pre class="bg-dark text-white p-3 rounded"><code>POST /api/v1/webhooks/test</code></pre>

            <h5 class="mt-4 mb-2">@lang('Required Auth Headers')</h5>
            <pre class="bg-dark text-white p-3 rounded"><code>X-API-KEY: cpk_xxxxx
X-API-TIMESTAMP: 2026-03-09T12:00:00Z
X-API-NONCE: 8c6eb6fd52f64b80
X-API-SIGNATURE: &lt;hmac_sha256_signature&gt;
Idempotency-Key: idem_47a4f8fbe71f</code></pre>
        </div>
    </div>
</section>
@endsection
