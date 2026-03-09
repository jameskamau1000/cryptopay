# Chain UAT Matrix

Use this matrix before enabling production traffic.

## Deposit Scenarios (per chain)
- Create invoice with chain + asset.
- Send expected amount.
- Verify:
  - deposit record created
  - confirmations increase
  - invoice moves to `paid`
  - `invoice.paid` webhook is sent

## Payout Scenarios (per chain)
- Create payout with valid destination.
- Verify:
  - on-chain payout row is created
  - tx hash assigned
  - status moves to `confirmed`
  - payout moves to `completed`
  - `payout.completed` webhook is sent

## Negative Scenarios
- Invalid destination address.
- Insufficient signer permissions/funds.
- Provider timeout and retry behavior.
