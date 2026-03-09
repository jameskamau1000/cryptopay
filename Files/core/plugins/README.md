# CryptoPay Plugin SDK and Connectors

This folder contains the plugin integration baseline for Minfee parity.

## Included

- `sdk/php/CryptoPayClient.php`: lightweight SDK for invoice and payout calls.
- OpenAPI source in `docs/openapi/minfee-parity-v1.yaml`.

## Connector rollout order

1. WooCommerce
2. OpenCart
3. Magento
4. PrestaShop
5. VirtueMart

## Connector contract

- Use API key + HMAC headers (`X-API-*`).
- Use `Idempotency-Key` for write requests.
- Handle webhook `X-CryptoPay-Signature` validation.
- Surface invoice and payout lifecycle statuses in storefront/admin.
