# CryptoPay Incident Runbook

## Payout Stuck
1. Open `CryptoPay Operations -> On-chain Payouts`.
2. Identify rows in `broadcasted/pending` beyond SLA.
3. Run `php artisan cryptopay:onchain:confirmations --limit=500`.
4. If provider outage is confirmed, freeze payouts from admin.

## Deposit Not Confirming
1. Open `CryptoPay Operations -> On-chain Deposits`.
2. Check confirmations against chain explorer.
3. Run:
   - `php artisan cryptopay:onchain:scan-deposits --limit=500`
   - `php artisan cryptopay:onchain:confirmations --limit=500`

## Webhook Backlog
1. Open `CryptoPay Operations -> Webhook Deliveries`.
2. Retry dead-letter entries where appropriate.
3. Check endpoint status and rotate secret if compromised.

## Signer Outage
1. Run `php artisan cryptopay:chain:health-check --chain=signer`.
2. If failing, freeze payouts until signer is healthy.
3. Escalate to signer service owner.
