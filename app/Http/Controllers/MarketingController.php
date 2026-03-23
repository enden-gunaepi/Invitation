<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Template;
use Illuminate\Http\Request;

class MarketingController extends Controller
{
    public function home()
    {
        $niches = $this->niches();
        $templates = Template::where('is_active', true)
            ->orderByDesc('is_premium')
            ->orderBy('name')
            ->limit(6)
            ->get();
        $packages = Package::where('is_active', true)
            ->orderBy('price')
            ->get();

        return view('marketing.home', compact('niches', 'templates', 'packages'));
    }

    public function niche(string $niche)
    {
        $niches = $this->niches();
        abort_unless(isset($niches[$niche]), 404);

        $item = $niches[$niche];

        return view('marketing.niche', compact('item', 'niche'));
    }

    public function trial()
    {
        $niches = $this->niches();
        return view('marketing.trial', compact('niches'));
    }

    public function trialPreview(Request $request)
    {
        $niches = $this->niches();
        $validated = $request->validate([
            'niche' => 'required|in:wedding,aqiqah,birthday,corporate',
            'title' => 'required|string|max:120',
            'host_1' => 'nullable|string|max:80',
            'host_2' => 'nullable|string|max:80',
            'event_date' => 'required|date',
            'venue_name' => 'required|string|max:120',
            'city' => 'nullable|string|max:80',
        ]);

        $item = $niches[$validated['niche']];
        $data = array_merge($validated, [
            'theme_color' => $item['color'],
            'headline' => $item['headline'],
        ]);

        return view('marketing.trial-preview', compact('data', 'item'));
    }

    private function niches(): array
    {
        return [
            'wedding' => [
                'name' => 'Wedding',
                'headline' => 'Undangan pernikahan elegan dengan RSVP otomatis',
                'description' => 'Template romantis, love story, galeri, gift, dan QR check-in.',
                'color' => '#f97316',
                'icon' => 'fa-heart',
                'highlights' => ['Love Story', 'RSVP + Ucapan', 'Gift & Transfer', 'Background music'],
            ],
            'aqiqah' => [
                'name' => 'Aqiqah',
                'headline' => 'Undangan aqiqah yang hangat dan rapi',
                'description' => 'Layout islami, detail acara, maps, dan konfirmasi kehadiran.',
                'color' => '#16a34a',
                'icon' => 'fa-moon',
                'highlights' => ['Susunan acara', 'Maps lokasi', 'Konfirmasi tamu', 'Share WhatsApp'],
            ],
            'birthday' => [
                'name' => 'Birthday',
                'headline' => 'Undangan ulang tahun fun dan interaktif',
                'description' => 'Desain playful, countdown, galeri foto, dan RSVP cepat.',
                'color' => '#06b6d4',
                'icon' => 'fa-cake-candles',
                'highlights' => ['Template ceria', 'Countdown', 'Galeri momen', 'RSVP praktis'],
            ],
            'corporate' => [
                'name' => 'Corporate',
                'headline' => 'Undangan event profesional untuk bisnis',
                'description' => 'Brand-ready, schedule detail, CTA registrasi, dan analytics.',
                'color' => '#6366f1',
                'icon' => 'fa-briefcase',
                'highlights' => ['Brand section', 'Run down acara', 'Registrasi tamu', 'Data analytics'],
            ],
        ];
    }
}
