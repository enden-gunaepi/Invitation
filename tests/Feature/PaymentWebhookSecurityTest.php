<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentWebhookSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_xendit_callback_is_idempotent(): void
    {
        Setting::set('xendit_callback_token', 'test-token', 'payment');

        [$payment] = $this->seedPaymentForGateway('xendit', 'INV-TEST-X-1');

        $payload = [
            'id' => 'evt-xendit-1',
            'external_id' => 'INV-TEST-X-1',
            'status' => 'PAID',
            'paid_amount' => 100000,
            'amount' => 100000,
        ];

        $this->postJson('/callback/xendit', $payload, [
            'x-callback-token' => 'test-token',
        ])->assertOk();

        $this->postJson('/callback/xendit', $payload, [
            'x-callback-token' => 'test-token',
        ])->assertOk();

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'payment_status' => 'paid',
        ]);
        $this->assertDatabaseCount('payment_callback_receipts', 1);
        $this->assertDatabaseCount('affiliate_commissions', 1);
    }

    public function test_tripay_callback_signature_and_idempotency(): void
    {
        Setting::set('tripay_private_key', 'tripay-private', 'payment');

        [$payment] = $this->seedPaymentForGateway('tripay', 'INV-TEST-T-1');

        $payload = [
            'reference' => 'TRX-REF-1',
            'merchant_ref' => 'INV-TEST-T-1',
            'status' => 'PAID',
            'total_amount' => 100000,
        ];
        $jsonBody = json_encode($payload, JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha256', $jsonBody, 'tripay-private');

        $this->call('POST', '/callback/tripay', [], [], [], [
            'HTTP_X-Callback-Signature' => $signature,
            'CONTENT_TYPE' => 'application/json',
        ], $jsonBody)->assertOk();

        $this->call('POST', '/callback/tripay', [], [], [], [
            'HTTP_X-Callback-Signature' => $signature,
            'CONTENT_TYPE' => 'application/json',
        ], $jsonBody)->assertOk();

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'payment_status' => 'paid',
        ]);
        $this->assertDatabaseCount('payment_callback_receipts', 1);
        $this->assertDatabaseCount('affiliate_commissions', 1);
    }

    /**
     * @return array{0: Payment}
     */
    private function seedPaymentForGateway(string $gateway, string $transactionId): array
    {
        $referrer = User::create([
            'name' => 'Referrer',
            'email' => 'referrer@example.test',
            'password' => 'password',
            'role' => 'client',
            'signup_ip' => '10.0.0.1',
            'signup_ua_hash' => hash('sha256', 'ua-referrer'),
        ]);

        $buyer = User::create([
            'name' => 'Buyer',
            'email' => 'buyer@example.test',
            'password' => 'password',
            'role' => 'client',
            'referred_by_user_id' => $referrer->id,
            'signup_ip' => '10.0.0.2',
            'signup_ua_hash' => hash('sha256', 'ua-buyer'),
        ]);

        $template = Template::create([
            'name' => 'Template Test',
            'slug' => 'template-test',
            'category' => 'wedding',
            'html_path' => 'invitations.templates.wedding-elegant.index',
        ]);

        $package = Package::create([
            'name' => 'Paket Test',
            'slug' => 'paket-test',
            'price' => 100000,
            'max_guests' => 100,
            'max_photos' => 10,
            'max_invitations' => 1,
            'affiliate_commission_rate' => 10,
            'tier' => 'starter',
            'billing_type' => 'one_time',
            'is_active' => true,
        ]);

        $invitation = Invitation::create([
            'user_id' => $buyer->id,
            'template_id' => $template->id,
            'package_id' => $package->id,
            'slug' => 'test-' . strtolower($gateway) . '-inv',
            'event_type' => 'wedding',
            'title' => 'Undangan Test',
            'event_date' => now()->addDays(10)->toDateString(),
            'event_time' => '10:00',
            'venue_name' => 'Gedung Test',
            'venue_address' => 'Jl. Test',
            'status' => 'pending',
        ]);

        $payment = Payment::create([
            'user_id' => $buyer->id,
            'invitation_id' => $invitation->id,
            'package_id' => $package->id,
            'amount' => 100000,
            'base_amount' => 100000,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'total_amount' => 100000,
            'payment_method' => 'qris',
            'payment_gateway' => $gateway,
            'payment_channel' => 'QRIS',
            'payment_status' => 'pending',
            'transaction_id' => $transactionId,
            'referral_code' => $referrer->referral_code,
            'affiliate_commission_amount' => 0,
        ]);

        return [$payment];
    }
}
