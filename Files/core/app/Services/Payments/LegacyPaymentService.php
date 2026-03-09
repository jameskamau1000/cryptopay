<?php

namespace App\Services\Payments;

use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\User;

class LegacyPaymentService
{
    public function creditMerchantBalance(User $user, Deposit $deposit, float $amount, string $remark = 'deposit')
    {
        $user->balance += $amount;
        $user->save();

        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge = $deposit->charge ?? 0;
        $transaction->trx_type = '+';
        $transaction->remark = $remark;
        $transaction->details = 'Payment credited via legacy flow';
        $transaction->trx = $deposit->trx;
        $transaction->save();
    }
}
