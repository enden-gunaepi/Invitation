<?php

namespace App\Services;

class GuestPersonalizationService
{
    public function forCategory(?string $category): array
    {
        $key = strtolower(trim((string) $category));

        return match ($key) {
            'keluarga' => [
                'greeting' => 'Keluarga Tercinta',
                'cta' => 'Konfirmasi Kehadiran Keluarga',
                'rsvp_hint' => 'Mohon konfirmasi kehadiran keluarga agar kami dapat menyiapkan tempat terbaik.',
            ],
            'rekan', 'rekan_kerja', 'kantor' => [
                'greeting' => 'Rekan Kerja Terhormat',
                'cta' => 'Konfirmasi Kehadiran Rekan',
                'rsvp_hint' => 'Konfirmasi kehadiran Anda membantu kami menyambut rekan-rekan dengan nyaman.',
            ],
            'vendor' => [
                'greeting' => 'Partner Vendor',
                'cta' => 'Konfirmasi Kehadiran Tim Vendor',
                'rsvp_hint' => 'Mohon konfirmasi tim yang hadir agar koordinasi acara berjalan lancar.',
            ],
            default => [
                'greeting' => 'Tamu Undangan',
                'cta' => 'Konfirmasi Kehadiran',
                'rsvp_hint' => 'Mohon konfirmasi kehadiran Anda melalui formulir RSVP di bawah ini.',
            ],
        };
    }
}
