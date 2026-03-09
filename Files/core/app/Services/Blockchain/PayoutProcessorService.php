<?php

namespace App\Services\Blockchain;

use App\Models\CustodyWallet;
use App\Models\OnchainPayout;
use App\Models\Payout;
use App\Services\Webhook\WebhookEventService;
use RuntimeException;

class PayoutProcessorService
{
    public function __construct(
        private ChainManager $chainManager,
        private WalletVaultService $walletVaultService,
        private ChainSignerService $chainSignerService,
        private WebhookEventService $webhookEventService
    ) {
    }

    public function process(Payout $payout): OnchainPayout
    {
        $chain = strtolower($payout->network ?: ($payout->metadata['chain'] ?? ''));
        $chain = $chain === 'bep20' ? 'bsc' : $chain;
        $asset = strtoupper($payout->asset ?: config('chains.default_asset', 'USDT'));
        if (!$chain) {
            throw new RuntimeException('Payout chain/network is required');
        }

        $wallet = CustodyWallet::where('chain', $chain)
            ->where('asset', $asset)
            ->where('is_active', 1)
            ->where('is_treasury', 1)
            ->first();

        if (!$wallet) {
            throw new RuntimeException('No active treasury wallet configured for ' . strtoupper($chain) . ' ' . $asset);
        }

        $adapter = $this->chainManager->for($chain);
        $privateKey = $this->walletVaultService->decryptPrivateKey($wallet);
        $amountString = number_format((float) $payout->net_amount, 8, '.', '');
        $signedRawTx = $payout->metadata['signed_raw_tx'] ?? null;
        $signedBoc = $payout->metadata['signed_boc'] ?? null;

        if (!$signedRawTx && !$signedBoc && config('chains.signer.enabled')) {
            $signed = $this->chainSignerService->sign(
                $chain,
                $asset,
                (string) $wallet->address,
                (string) $payout->destination,
                $amountString,
                $privateKey,
                ['payout_id' => $payout->id, 'wallet_id' => $wallet->id]
            );
            $signedRawTx = $signed['signed_raw_tx'] ?? null;
            $signedBoc = $signed['signed_boc'] ?? null;
        }

        $result = $adapter->sendAsset([
            'asset' => $asset,
            'amount' => (float) $payout->net_amount,
            'amount_str' => $amountString,
            'to_address' => $payout->destination,
            'from_address' => $wallet->address,
            'wallet_id' => $wallet->id,
            'payout_id' => $payout->id,
            'signed_raw_tx' => $signedRawTx,
            'signed_boc' => $signedBoc,
            'private_key' => $privateKey,
        ]);

        $onchainPayout = OnchainPayout::updateOrCreate(
            ['payout_id' => $payout->id],
            [
                'user_id' => $payout->user_id,
                'from_wallet_id' => $wallet->id,
                'chain' => $chain,
                'asset' => $asset,
                'to_address' => $payout->destination,
                'tx_hash' => $result['tx_hash'] ?? null,
                'amount' => (float) $payout->net_amount,
                'fee' => (float) ($result['fee'] ?? 0),
                'status' => 'broadcasted',
                'payload' => $result['payload'] ?? null,
                'broadcasted_at' => now(),
            ]
        );

        $payout->status = 'processing';
        $payout->save();

        $this->webhookEventService->publish(
            $payout->user,
            'payout.broadcasted',
            'payout',
            $payout->id,
            $payout->fresh()->toArray()
        );

        return $onchainPayout;
    }
}
