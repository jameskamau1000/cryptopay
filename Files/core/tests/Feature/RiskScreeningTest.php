<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Risk\RiskScreeningService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RiskScreeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_high_value_invoice_creates_risk_case(): void
    {
        $user = User::create(['username' => 'risk_user', 'email' => 'risk@test.local']);
        $service = new RiskScreeningService();
        $service->flagHighValueInvoice($user, 99, 7000);

        $this->assertDatabaseHas('risk_cases', [
            'entity_type' => 'invoice',
            'entity_id' => 99,
            'rule_code' => 'HIGH_VALUE_INVOICE',
        ]);
    }
}
