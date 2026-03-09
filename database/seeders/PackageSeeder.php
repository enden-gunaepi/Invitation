<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        Package::create([
            'name' => 'Starter',
            'slug' => 'starter',
            'tier' => 'starter',
            'description' => 'Paket awal untuk kebutuhan undangan personal dengan fitur inti.',
            'badge_text' => 'Mulai cepat tanpa ribet',
            'support_level' => 'Support Email',
            'sla_hours' => 48,
            'price' => 99000,
            'max_guests' => 100,
            'max_photos' => 5,
            'max_invitations' => 1,
            'features' => ['1 Template', 'RSVP', 'Ucapan', 'Google Maps', 'Share Link'],
            'addons' => ['Import tamu via Excel', 'Analytics dasar'],
            'is_active' => true,
        ]);

        Package::create([
            'name' => 'Growth',
            'slug' => 'growth',
            'tier' => 'growth',
            'description' => 'Paket untuk kebutuhan acara menengah dengan fitur marketing lebih lengkap.',
            'badge_text' => 'Paling favorit',
            'support_level' => 'Priority Chat',
            'sla_hours' => 24,
            'price' => 199000,
            'max_guests' => 500,
            'max_photos' => 20,
            'max_invitations' => 2,
            'features' => ['Semua Template', 'RSVP', 'Ucapan', 'Google Maps', 'Share Link', 'Background Music', 'Countdown Timer', 'Custom Domain'],
            'addons' => ['Template premium', 'Gift & transfer section', 'Export data RSVP'],
            'is_recommended' => true,
            'is_active' => true,
        ]);

        Package::create([
            'name' => 'Pro',
            'slug' => 'pro',
            'tier' => 'pro',
            'description' => 'Paket profesional untuk event besar dengan dukungan prioritas.',
            'badge_text' => 'Untuk event skala besar',
            'support_level' => 'Priority WhatsApp',
            'sla_hours' => 12,
            'price' => 399000,
            'max_guests' => 2000,
            'max_photos' => 50,
            'max_invitations' => 5,
            'features' => ['Semua Template Premium', 'RSVP', 'Ucapan', 'Google Maps', 'Share Link', 'Background Music', 'Countdown Timer', 'Custom Domain', 'Galeri Video', 'Animasi Premium', 'Priority Support'],
            'addons' => ['White-label ringan', 'Multi-admin akses tim', 'Onboarding assisted'],
            'is_active' => true,
        ]);
    }
}
