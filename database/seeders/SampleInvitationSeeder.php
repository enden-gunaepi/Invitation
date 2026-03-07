<?php

namespace Database\Seeders;

use App\Models\Invitation;
use App\Models\Wish;
use Illuminate\Database\Seeder;

class SampleInvitationSeeder extends Seeder
{
    public function run(): void
    {
        // Template 1: Wedding Elegant (template_id = 1)
        $inv1 = Invitation::create([
            'user_id' => 2, 'template_id' => 1, 'package_id' => 2,
            'event_type' => 'wedding', 'title' => 'Pernikahan Ahmad & Siti',
            'slug' => 'ahmad-siti', 'groom_name' => 'Ahmad Fadhil', 'bride_name' => 'Siti Nurhaliza',
            'event_date' => '2026-06-15', 'event_time' => '10:00',
            'venue_name' => 'Hotel Grand Ballroom', 'venue_address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
            'opening_text' => 'Assalamualaikum Warahmatullahi Wabarakatuh. Dengan memohon rahmat dan ridho Allah SWT, kami bermaksud menyelenggarakan acara pernikahan putra-putri kami.',
            'closing_text' => 'Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir.',
            'status' => 'active', 'view_count' => 42,
        ]);

        // Template 2: Wedding Rustic (template_id = 2)
        $inv2 = Invitation::create([
            'user_id' => 2, 'template_id' => 2, 'package_id' => 3,
            'event_type' => 'wedding', 'title' => 'Pernikahan Reza & Amelia',
            'slug' => 'reza-amelia', 'groom_name' => 'Reza Putra', 'bride_name' => 'Amelia Zahra',
            'event_date' => '2026-08-20', 'event_time' => '09:00',
            'venue_name' => 'Taman Puri Botanical Garden', 'venue_address' => 'Jl. Raya Bogor Km 30, Bogor',
            'opening_text' => 'Bismillahirrahmanirrahim. Dengan mengharap ridho Allah SWT, kami mengundang Bapak/Ibu/Saudara/i untuk menghadiri resepsi pernikahan kami.',
            'closing_text' => 'Terima kasih atas doa restu yang diberikan. Semoga Allah SWT membalas kebaikan Anda.',
            'status' => 'active', 'view_count' => 28,
        ]);

        // Template 3: Birthday Fun (template_id = 3)
        $inv3 = Invitation::create([
            'user_id' => 3, 'template_id' => 3, 'package_id' => 1,
            'event_type' => 'birthday', 'title' => 'Happy 7th Birthday Aisyah!',
            'slug' => 'birthday-aisyah', 'host_name' => 'Aisyah Zahra',
            'event_date' => '2026-07-10', 'event_time' => '14:00',
            'venue_name' => 'FunWorld Arena', 'venue_address' => 'Mall Grand Indonesia Lt. 5, Jakarta',
            'opening_text' => 'Hai teman-teman! Aisyah mau ngadain pesta ulang tahun yang seru banget! Yuk datang dan ramaikan!',
            'closing_text' => 'Ditunggu kehadirannya ya! Jangan lupa bawa semangat dan senyum 😄',
            'status' => 'active', 'view_count' => 15,
        ]);

        // Template 4: Wedding Minimalist (template_id = 4)
        $inv4 = Invitation::create([
            'user_id' => 3, 'template_id' => 4, 'package_id' => 3,
            'event_type' => 'wedding', 'title' => 'Pernikahan Daniel & Sarah',
            'slug' => 'daniel-sarah', 'groom_name' => 'Daniel Hartono', 'bride_name' => 'Sarah Wijaya',
            'event_date' => '2026-09-05', 'event_time' => '11:00',
            'venue_name' => 'The Mulia Resort', 'venue_address' => 'Jl. Raya Nusa Dua Selatan, Bali',
            'opening_text' => 'Together with our families, we joyfully invite you to celebrate our union in marriage.',
            'closing_text' => 'Your presence and blessings would mean the world to us.',
            'status' => 'active', 'view_count' => 35,
        ]);

        // Sample wishes for all invitations
        $wishes = [
            ['name' => 'Budi Santoso', 'message' => 'Selamat menempuh hidup baru! Barakallah.'],
            ['name' => 'Dewi Lestari', 'message' => 'Happy wedding! Semoga langgeng dan bahagia selalu 💐'],
            ['name' => 'Rizky Pratama', 'message' => 'Congratulations! Semoga menjadi keluarga sakinah mawaddah warahmah 🎉'],
        ];

        foreach ([$inv1, $inv2, $inv3, $inv4] as $inv) {
            foreach ($wishes as $wish) {
                Wish::create(array_merge($wish, [
                    'invitation_id' => $inv->id,
                    'is_approved' => true,
                ]));
            }
        }
    }
}
