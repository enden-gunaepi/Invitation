<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Invitation;
use App\Models\InvitationBankAccount;
use App\Models\InvitationEvent;
use App\Models\InvitationPhoto;
use App\Models\LoveStory;
use App\Models\Rsvp;
use App\Models\Template;
use App\Models\Wish;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TemplateDemoController extends Controller
{
    public function show(Request $request, Template $template)
    {
        abort_if(!$template->is_active, 404);
        abort_unless(view()->exists($template->html_path), 404);

        $now = Carbon::now();
        $eventDate = $now->copy()->addDays(30);

        $invitation = new Invitation([
            'id' => 0,
            'slug' => 'demo-' . $template->slug,
            'event_type' => 'wedding',
            'title' => 'Demo ' . $template->name,
            'groom_name' => 'Raka Pratama',
            'groom_parent_name' => 'Putra Bpk. Hendra & Ibu Sari',
            'bride_name' => 'Alya Nirmala',
            'bride_parent_name' => 'Putri Bpk. Yusuf & Ibu Lina',
            'host_name' => 'Keluarga Besar',
            'event_date' => $eventDate,
            'event_time' => '09:00',
            'event_end_time' => '12:00',
            'venue_name' => 'Grand Ballroom Harmoni',
            'venue_address' => 'Jl. Mawar No. 123, Bandung',
            'google_maps_url' => 'https://maps.google.com/?q=Bandung',
            'opening_text' => 'Dengan memohon rahmat dan ridho Allah SWT, kami bermaksud mengundang Bapak/Ibu/Saudara/i pada acara pernikahan kami.',
            'closing_text' => 'Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir.',
            'footer_text' => 'Terima kasih atas doa dan restunya.',
            'status' => 'active',
            'published_at' => $now,
            'expires_at' => $eventDate->copy()->addDays(14),
            'cover_photo' => $template->thumbnail,
            'groom_photo' => $template->thumbnail,
            'bride_photo' => $template->thumbnail,
            'venue_lat' => -6.917464,
            'venue_lng' => 107.619125,
        ]);

        $invitation->setRelation('template', $template);
        $invitation->setRelation('photos', collect([
            new InvitationPhoto(['file_path' => $template->thumbnail, 'caption' => 'Momen 1']),
            new InvitationPhoto(['file_path' => $template->thumbnail, 'caption' => 'Momen 2']),
            new InvitationPhoto(['file_path' => $template->thumbnail, 'caption' => 'Momen 3']),
        ]));
        $invitation->setRelation('events', collect([
            new InvitationEvent([
                'event_name' => 'Akad Nikah',
                'event_description' => 'Prosesi akad nikah',
                'event_date' => $eventDate,
                'event_time' => '09:00',
                'venue_name' => 'Grand Ballroom Harmoni',
                'venue_address' => 'Jl. Mawar No. 123, Bandung',
            ]),
            new InvitationEvent([
                'event_name' => 'Resepsi',
                'event_description' => 'Resepsi dan ramah tamah',
                'event_date' => $eventDate,
                'event_time' => '11:00',
                'venue_name' => 'Grand Ballroom Harmoni',
                'venue_address' => 'Jl. Mawar No. 123, Bandung',
            ]),
        ]));
        $invitation->setRelation('loveStories', collect([
            new LoveStory([
                'year' => '2020',
                'title' => 'Pertemuan Pertama',
                'description' => 'Awal perkenalan kami dimulai dari pertemuan sederhana.',
                'photo_path' => $template->thumbnail,
            ]),
            new LoveStory([
                'year' => '2024',
                'title' => 'Lamaran',
                'description' => 'Dengan izin Allah, kami melangkah ke jenjang yang lebih serius.',
                'photo_path' => $template->thumbnail,
            ]),
        ]));
        $invitation->setRelation('bankAccounts', collect([
            new InvitationBankAccount([
                'bank_name' => 'BCA',
                'account_number' => '1234567890',
                'account_name' => 'Raka Pratama',
            ]),
        ]));
        $invitation->setRelation('rsvps', collect([
            new Rsvp(['name' => 'Budi', 'status' => 'attending', 'pax' => 2, 'message' => 'Bandung']),
            new Rsvp(['name' => 'Citra', 'status' => 'maybe', 'pax' => 1, 'message' => 'Jakarta']),
        ]));
        $invitation->setRelation('wishes', collect([
            new Wish(['name' => 'Dina', 'message' => 'Semoga menjadi keluarga sakinah mawaddah warahmah.']),
            new Wish(['name' => 'Eko', 'message' => 'Lancar sampai hari H dan bahagia selalu.']),
        ]));

        $guest = new Guest([
            'id' => null,
            'name' => 'Tamu Demo',
            'token' => 'demo-guest-token',
        ]);
        $guest->setRelation('invitation', $invitation);

        return view($template->html_path, [
            'invitation' => $invitation,
            'guest' => $guest,
            'demoMode' => true,
        ]);
    }
}
