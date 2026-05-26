<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Template;
use App\Models\User;
use App\Models\BalanceTransaction;
use App\Services\BalanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BalanceSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_adjust_client_balance(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $client = User::create([
            'name' => 'Client User',
            'email' => 'client@example.test',
            'password' => bcrypt('password'),
            'role' => 'client',
            'balance' => 0.00,
        ]);

        $this->actingAs($admin);

        // Test Add Balance
        $response = $this->post("/admin/balance/{$client->id}/adjust", [
            'action_type' => 'add',
            'amount' => 150000,
            'admin_note' => 'Penambahan saldo promo',
        ]);

        $response->assertSessionHasNoErrors();
        $client->refresh();
        $this->assertEquals(150000.00, (float) $client->balance);

        $this->assertDatabaseHas('balance_transactions', [
            'user_id' => $client->id,
            'type' => BalanceTransaction::TYPE_ADJUSTMENT,
            'amount' => 150000.00,
            'balance_before' => 0.00,
            'balance_after' => 150000.00,
            'performed_by' => $admin->id,
            'admin_note' => 'Penambahan saldo promo',
        ]);

        // Test Subtract Balance
        $response = $this->post("/admin/balance/{$client->id}/adjust", [
            'action_type' => 'subtract',
            'amount' => 50000,
            'admin_note' => 'Koreksi kelebihan saldo',
        ]);

        $response->assertSessionHasNoErrors();
        $client->refresh();
        $this->assertEquals(100000.00, (float) $client->balance);

        $this->assertDatabaseHas('balance_transactions', [
            'user_id' => $client->id,
            'type' => BalanceTransaction::TYPE_ADJUSTMENT,
            'amount' => -50000.00,
            'balance_before' => 150000.00,
            'balance_after' => 100000.00,
            'performed_by' => $admin->id,
            'admin_note' => 'Koreksi kelebihan saldo',
        ]);

        // Test Subtract Over Balance (Insufficient)
        $response = $this->post("/admin/balance/{$client->id}/adjust", [
            'action_type' => 'subtract',
            'amount' => 200000,
            'admin_note' => 'Koreksi berlebih',
        ]);

        $response->assertSessionHas('error', 'Gagal memotong saldo: Saldo user tidak mencukupi.');
        $client->refresh();
        $this->assertEquals(100000.00, (float) $client->balance);
    }

    public function test_topup_payment_callback_credits_balance(): void
    {
        Setting::set('xendit_callback_token', 'test-token-topup', 'payment');

        $client = User::create([
            'name' => 'Client Topup',
            'email' => 'topup@example.test',
            'password' => bcrypt('password'),
            'role' => 'client',
            'balance' => 0.00,
        ]);

        $payment = Payment::create([
            'user_id' => $client->id,
            'amount' => 250000,
            'base_amount' => 250000,
            'total_amount' => 250000,
            'payment_method' => 'bank_transfer',
            'payment_gateway' => 'xendit',
            'payment_status' => 'pending',
            'payment_purpose' => Payment::PURPOSE_TOPUP,
            'transaction_id' => 'TRX-TOPUP-1',
        ]);

        $payload = [
            'id' => 'evt-topup-1',
            'external_id' => 'TRX-TOPUP-1',
            'status' => 'PAID',
            'paid_amount' => 250000,
            'amount' => 250000,
        ];

        $response = $this->postJson('/callback/xendit', $payload, [
            'x-callback-token' => 'test-token-topup',
        ]);

        $response->assertOk();
        
        $client->refresh();
        $this->assertEquals(250000.00, (float) $client->balance);

        $this->assertDatabaseHas('balance_transactions', [
            'user_id' => $client->id,
            'type' => BalanceTransaction::TYPE_TOPUP,
            'amount' => 250000.00,
            'balance_before' => 0.00,
            'balance_after' => 250000.00,
            'reference_type' => 'payment',
            'reference_id' => $payment->id,
        ]);
    }
}
