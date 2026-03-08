<?php

namespace Database\Seeders;

use App\Models\Invitation;
use App\Models\User;
use App\Models\Wish;
use Illuminate\Database\Seeder;

class SampleInvitationSeeder extends Seeder
{
    /**
     * Seed sample invitations using existing registered users.
     * This seeder does NOT create any users — users must register first.
     * Run manually: php artisan db:seed --class=SampleInvitationSeeder
     */
    public function run(): void
    {
        // Require at least one client user to exist
        $client = User::where('role', 'client')->first();

        if (!$client) {
            $this->command->warn('No client users found. Register a client user first, then run this seeder.');
            return;
        }

        // Use the first client for all sample invitations
        $userId = $client->id;

        // Template 1: Wedding Elegant
        $inv1 = Invitation::firstOrCreate(
            ['slug' => 'ahmad-siti'],
            [
                'user_id' => $userId, 'template_id' => 1, 'package_id' => 2,
                'event_type' => 'wedding', 'title' => 'Pernikahan Ahmad & Siti',
                'groom_name' => 'Ahmad Fadhil', 'bride_name' => 'Siti Nurhaliza',
                'event_date' => '2026-06-15', 'event_time' => '10:00',
                'venue_name' => 'Hotel Grand Ballroom', 'venue_address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'opening_text' => 'Assalamualaikum Warahmatullahi Wabarakatuh. Dengan memohon rahmat dan ridho Allah SWT, kami bermaksud menyelenggarakan acara pernikahan putra-putri kami.',
                'closing_text' => 'Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir.',
                'status' => 'active', 'view_count' => 42,
            ]
        );

        // Template 2: Wedding Rustic
        $inv2 = Invitation::firstOrCreate(
            ['slug' => 'reza-amelia'],
            [
                'user_id' => $userId, 'template_id' => 2, 'package_id' => 3,
                'event_type' => 'wedding', 'title' => 'Pernikahan Reza & Amelia',
                'groom_name' => 'Reza Putra', 'bride_name' => 'Amelia Zahra',
                'event_date' => '2026-08-20', 'event_time' => '09:00',
                'venue_name' => 'Taman Puri Botanical Garden', 'venue_address' => 'Jl. Raya Bogor Km 30, Bogor',
                'opening_text' => 'Bismillahirrahmanirrahim. Dengan mengharap ridho Allah SWT, kami mengundang Bapak/Ibu/Saudara/i untuk menghadiri resepsi pernikahan kami.',
                'closing_text' => 'Terima kasih atas doa restu yang diberikan.',
                'status' => 'active', 'view_count' => 28,
            ]
        );

        // Sample wishes
        $wishes = [
            ['name' => 'Budi Santoso', 'message' => 'Selamat menempuh hidup baru! Barakallah.'],
            ['name' => 'Dewi Lestari', 'message' => 'Happy wedding! Semoga langgeng dan bahagia selalu 💐'],
            ['name' => 'Rizky Pratama', 'message' => 'Congratulations! Semoga sakinah mawaddah warahmah 🎉'],
        ];

        foreach ([$inv1, $inv2] as $inv) {
            if ($inv->wishes()->count() === 0) {
                foreach ($wishes as $wish) {
                    Wish::create(array_merge($wish, [
                        'invitation_id' => $inv->id,
                        'is_approved' => true,
                    ]));
                }
            }
        }

        $this->command->info('Sample invitations seeded for user: ' . $client->email);
    }
}
