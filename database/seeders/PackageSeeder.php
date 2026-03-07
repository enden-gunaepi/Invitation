<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        Package::create([
            'name' => 'Basic',
            'slug' => 'basic',
            'description' => 'Paket dasar untuk undangan digital sederhana. Cocok untuk acara kecil.',
            'price' => 99000,
            'max_guests' => 100,
            'max_photos' => 5,
            'features' => ['1 Template', 'RSVP', 'Ucapan', 'Google Maps', 'Share Link'],
            'is_active' => true,
        ]);

        Package::create([
            'name' => 'Premium',
            'slug' => 'premium',
            'description' => 'Paket lengkap dengan fitur premium. Cocok untuk acara menengah.',
            'price' => 199000,
            'max_guests' => 500,
            'max_photos' => 20,
            'features' => ['Semua Template', 'RSVP', 'Ucapan', 'Google Maps', 'Share Link', 'Background Music', 'Countdown Timer', 'Custom Domain'],
            'is_active' => true,
        ]);

        Package::create([
            'name' => 'Exclusive',
            'slug' => 'exclusive',
            'description' => 'Paket exclusive dengan semua fitur tanpa batas. Untuk acara besar & spesial.',
            'price' => 399000,
            'max_guests' => 2000,
            'max_photos' => 50,
            'features' => ['Semua Template Premium', 'RSVP', 'Ucapan', 'Google Maps', 'Share Link', 'Background Music', 'Countdown Timer', 'Custom Domain', 'Galeri Video', 'Animasi Premium', 'Priority Support'],
            'is_active' => true,
        ]);
    }
}
