# CryptoPay Production Go-Live Checklist

## 1) Security Baseline
- Set `APP_ENV=production`, `APP_DEBUG=false`, correct `APP_URL`.
- Set `ENFORCE_HTTPS=true`.
- Set `ADMIN_IP_ALLOWLIST` to trusted office/VPN IPs only.
- Rotate admin passwords, API keys, webhook secrets, and signer token.
- Verify encrypted wallet vault entries are populated for active treasury wallets.

## 2) Chain Credentials and Connectivity
- Configure all enabled chains in `.env`.
- Run `php artisan cryptopay:chain:health-check`.
- If unified signer is used, set `CHAIN_SIGNER_ENABLED=true` and signer values.

## 3) Live UAT Matrix
- Test small-value deposit and payout for each enabled chain.
- Verify webhook delivery and retries.
- Verify failed payout path (invalid destination / invalid signature).

## 4) Reconciliation
- Confirm cron runs:
  - `cryptopay:onchain:scan-deposits`
  - `cryptopay:onchain:confirmations`
  - `cryptopay:reconcile:daily-report`
- Check `storage/app/reports/reconciliation-YYYY-MM-DD.json`.

## 5) Monitoring and Alerts
- Confirm `cryptopay:monitor:health` runs every 5 minutes.
- Alert on non-zero exit code / error logs.

## 6) Operational Readiness
- Confirm payout freeze toggle works from `CryptoPay Operations -> Payouts`.
- Document incident contacts and escalation flow.

## 7) Release Gate
- Run `php artisan cryptopay:release:gate`.
- Launch only if all checks pass.
