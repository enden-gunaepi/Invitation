<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::updateOrCreate(
            ['code' => 'PROMO10'],
            [
                'name' => 'Diskon 10%',
                'discount_type' => 'percent',
                'discount_value' => 10,
                'max_discount_amount' => 50000,
                'min_transaction_amount' => 50000,
                'usage_limit' => 1000,
                'usage_per_user' => 1,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addMonths(3),
                'is_active' => true,
            ]
        );

        Coupon::updateOrCreate(
            ['code' => 'HEMAT25K'],
            [
                'name' => 'Potongan Rp25.000',
                'discount_type' => 'fixed',
                'discount_value' => 25000,
                'max_discount_amount' => null,
                'min_transaction_amount' => 100000,
                'usage_limit' => 500,
                'usage_per_user' => 1,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addMonths(3),
                'is_active' => true,
            ]
        );
    }
}

