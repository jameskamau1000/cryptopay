<?php

namespace App\Services\Blockchain;

use App\Models\CustodyWallet;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class WalletVaultService
{
    public function decryptPrivateKey(?CustodyWallet $wallet): ?string
    {
        if (!$wallet || !$wallet->encrypted_private_key) {
            return null;
        }

        try {
            return Crypt::decryptString($wallet->encrypted_private_key);
        } catch (Throwable) {
            return null;
        }
    }
}
