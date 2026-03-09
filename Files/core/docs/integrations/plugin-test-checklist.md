# Plugin Integration Test Checklist

Use this checklist for WooCommerce, OpenCart, Magento, PrestaShop, and VirtueMart connectors.

1. Generate scoped API key (`invoices:write`, `invoices:read`, `webhooks:write`).
2. Configure plugin with API base URL, public key, and secret key.
3. Place a checkout order and verify invoice is created (`/api/v1/invoices`).
4. Simulate payment success and verify plugin updates order state.
5. Trigger `POST /api/v1/webhooks/test` and validate signature verification.
6. Verify retries/dead-letter behavior for intentionally failing webhook URL.
7. Confirm idempotency handling by replaying same checkout request.
