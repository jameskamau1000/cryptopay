<?php

namespace App\Services\Risk;

use App\Models\RiskCase;
use App\Models\User;

class RiskScreeningService
{
    public function flagHighValueInvoice(User $user, int $invoiceId, float $amount): void
    {
        if ($amount < 5000) {
            return;
        }

        RiskCase::create([
            'user_id' => $user->id,
            'entity_type' => 'invoice',
            'entity_id' => $invoiceId,
            'rule_code' => 'HIGH_VALUE_INVOICE',
            'severity' => 'high',
            'status' => 'open',
            'summary' => 'Invoice amount crossed high value threshold',
            'evidence' => ['amount' => $amount],
        ]);
    }

    public function flagHighValuePayout(User $user, int $payoutId, float $amount): void
    {
        if ($amount < 2000) {
            return;
        }

        RiskCase::create([
            'user_id' => $user->id,
            'entity_type' => 'payout',
            'entity_id' => $payoutId,
            'rule_code' => 'HIGH_VALUE_PAYOUT',
            'severity' => 'high',
            'status' => 'open',
            'summary' => 'Payout amount crossed high value threshold',
            'evidence' => ['amount' => $amount],
        ]);
    }
}
