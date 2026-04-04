<?php

namespace App\Services\Planner;

use App\Models\Planner\WpBudgetCategory;
use App\Models\Planner\WpChecklistItem;
use App\Models\Planner\WpProfile;
use App\Models\Planner\WpTimelineEvent;
use Carbon\Carbon;

class PlannerOnboardingService
{
    /**
     * Generate all initial data after onboarding is complete.
     */
    public function generateInitialData(WpProfile $profile): void
    {
        $this->generateChecklist($profile);
        $this->generateBudgetCategories($profile);
        $this->generateTimeline($profile);
    }

    /**
     * Auto-generate smart checklist based on wedding date.
     */
    public function generateChecklist(WpProfile $profile): void
    {
        if (!$profile->wedding_date) {
            return;
        }

        $weddingDate = $profile->wedding_date;
        $items = $this->getChecklistTemplate($profile->concept);
        $sort = 0;

        foreach ($items as $item) {
            $deadline = $weddingDate->copy()->subDays($item['days_before']);
            // Don't create items with deadlines in the past (too late)
            // But DO create them anyway, marked as overdue for awareness
            WpChecklistItem::create([
                'wp_profile_id' => $profile->id,
                'title' => $item['title'],
                'description' => $item['description'] ?? null,
                'category' => $item['category'],
                'deadline' => $deadline,
                'status' => $deadline->isPast() ? 'done' : 'todo',
                'sort_order' => $sort++,
                'is_auto_generated' => true,
                'completed_at' => $deadline->isPast() ? now() : null,
            ]);
        }
    }

    /**
     * Auto-generate budget categories based on concept.
     */
    public function generateBudgetCategories(WpProfile $profile): void
    {
        $totalBudget = (float) $profile->total_budget;
        if ($totalBudget <= 0) {
            $totalBudget = 50000000; // Default 50jt
        }

        $percentages = $this->getBudgetPercentages($profile->concept);
        $sort = 0;

        foreach ($percentages as $cat) {
            WpBudgetCategory::create([
                'wp_profile_id' => $profile->id,
                'name' => $cat['name'],
                'estimated_amount' => round($totalBudget * ($cat['percent'] / 100)),
                'actual_amount' => 0,
                'sort_order' => $sort++,
                'icon' => $cat['icon'],
                'color' => $cat['color'],
            ]);
        }
    }

    /**
     * Auto-generate timeline milestones.
     */
    public function generateTimeline(WpProfile $profile): void
    {
        if (!$profile->wedding_date) {
            return;
        }

        $weddingDate = $profile->wedding_date;
        $milestones = [
            ['days' => 365, 'title' => 'Mulai Perencanaan', 'desc' => 'Tentukan budget, konsep, dan tanggal acara', 'cat' => 'general'],
            ['days' => 300, 'title' => 'Booking Venue', 'desc' => 'Survey dan booking tempat acara', 'cat' => 'venue'],
            ['days' => 270, 'title' => 'Pilih Catering', 'desc' => 'Tasting menu dan negosiasi harga', 'cat' => 'catering'],
            ['days' => 240, 'title' => 'Hire Fotografer & Videografer', 'desc' => 'Review portofolio dan booking tanggal', 'cat' => 'foto'],
            ['days' => 210, 'title' => 'Pilih Dekorasi', 'desc' => 'Konsultasi tema dan konsep dekorasi', 'cat' => 'dekor'],
            ['days' => 180, 'title' => 'Fitting Baju & MUA', 'desc' => 'Pilih gaun/jas dan trial makeup', 'cat' => 'busana'],
            ['days' => 150, 'title' => 'Entertainment & MC', 'desc' => 'Booking MC dan entertainment', 'cat' => 'entertainment'],
            ['days' => 120, 'title' => 'Siapkan Undangan Digital', 'desc' => 'Buat undangan digital di platform ini', 'cat' => 'undangan'],
            ['days' => 90, 'title' => 'Kirim Undangan', 'desc' => 'Kirim undangan ke semua tamu', 'cat' => 'undangan'],
            ['days' => 60, 'title' => 'Pesan Souvenir', 'desc' => 'Pilih dan pesan souvenir pernikahan', 'cat' => 'souvenir'],
            ['days' => 30, 'title' => 'Final Fitting', 'desc' => 'Final fitting baju dan trial makeup final', 'cat' => 'busana'],
            ['days' => 14, 'title' => 'Briefing Vendor', 'desc' => 'Koordinasi final dengan semua vendor', 'cat' => 'general'],
            ['days' => 7, 'title' => 'Rehearsal', 'desc' => 'Gladi bersih dan cek lokasi', 'cat' => 'general'],
            ['days' => 1, 'title' => 'Persiapan Akhir', 'desc' => 'Siapkan semua keperluan hari H', 'cat' => 'general'],
            ['days' => 0, 'title' => '✨ Hari Bahagia!', 'desc' => 'Selamat menempuh hidup baru!', 'cat' => 'general'],
        ];

        $sort = 0;
        foreach ($milestones as $m) {
            $targetDate = $weddingDate->copy()->subDays($m['days']);
            WpTimelineEvent::create([
                'wp_profile_id' => $profile->id,
                'title' => $m['title'],
                'description' => $m['desc'],
                'target_date' => $targetDate,
                'is_completed' => $targetDate->isPast(),
                'category' => $m['cat'],
                'sort_order' => $sort++,
            ]);
        }
    }

    /**
     * Get detailed checklist template based on concept.
     */
    private function getChecklistTemplate(string $concept): array
    {
        $base = [
            ['days_before' => 365, 'title' => 'Tentukan total budget pernikahan', 'category' => 'general', 'description' => 'Diskusikan bersama pasangan dan keluarga tentang budget keseluruhan'],
            ['days_before' => 360, 'title' => 'Tentukan tanggal dan konsep acara', 'category' => 'general', 'description' => 'Pilih tanggal yang sesuai dan tentukan tema pernikahan'],
            ['days_before' => 330, 'title' => 'Survey dan booking venue', 'category' => 'venue', 'description' => 'Kunjungi minimal 3-5 venue, bandingkan harga dan fasilitas'],
            ['days_before' => 300, 'title' => 'Bayar DP venue', 'category' => 'venue', 'description' => 'Selesaikan kontrak dan bayar uang muka'],
            ['days_before' => 280, 'title' => 'Pilih dan booking catering', 'category' => 'catering', 'description' => 'Lakukan food tasting di minimal 3 vendor catering'],
            ['days_before' => 270, 'title' => 'Booking fotografer & videografer', 'category' => 'foto', 'description' => 'Review portofolio, cek ketersediaan tanggal'],
            ['days_before' => 240, 'title' => 'Konsultasi dekorasi', 'category' => 'dekor', 'description' => 'Diskusikan tema, warna, dan konsep dekorasi'],
            ['days_before' => 210, 'title' => 'Pilih gaun/jas pengantin', 'category' => 'busana', 'description' => 'Fitting pertama dan pilih desain'],
            ['days_before' => 200, 'title' => 'Booking MUA (Makeup Artist)', 'category' => 'busana', 'description' => 'Trial makeup dan discuss look yang diinginkan'],
            ['days_before' => 180, 'title' => 'Booking MC dan entertainment', 'category' => 'entertainment', 'description' => 'Pilih MC yang sesuai dengan tema acara'],
            ['days_before' => 150, 'title' => 'Susun daftar tamu', 'category' => 'undangan', 'description' => 'Buat daftar tamu lengkap dengan kategori (keluarga, teman, dll)'],
            ['days_before' => 120, 'title' => 'Buat undangan digital', 'category' => 'undangan', 'description' => 'Buat undangan digital menarik di platform kami 💌'],
            ['days_before' => 100, 'title' => 'Kirim undangan digital', 'category' => 'undangan', 'description' => 'Kirimkan link undangan ke semua tamu'],
            ['days_before' => 90, 'title' => 'Cek RSVP tamu', 'category' => 'undangan', 'description' => 'Monitoring respon tamu dan follow up yang belum konfirmasi'],
            ['days_before' => 80, 'title' => 'Pesan souvenir', 'category' => 'souvenir', 'description' => 'Pilih souvenir dan pesan sesuai estimasi jumlah tamu'],
            ['days_before' => 60, 'title' => 'Pelunasan catering', 'category' => 'catering', 'description' => 'Finalisasi menu dan lunasi pembayaran'],
            ['days_before' => 45, 'title' => 'Fitting baju ke-2', 'category' => 'busana', 'description' => 'Cek fitting dan penyesuaian terakhir'],
            ['days_before' => 30, 'title' => 'Final fitting baju', 'category' => 'busana', 'description' => 'Fitting terakhir, pastikan semua pas'],
            ['days_before' => 30, 'title' => 'Pelunasan dekorasi', 'category' => 'dekor', 'description' => 'Finalisasi design dan lunasi pembayaran'],
            ['days_before' => 21, 'title' => 'Trial makeup final', 'category' => 'busana', 'description' => 'Final trial dan konfirmasi look'],
            ['days_before' => 14, 'title' => 'Briefing semua vendor', 'category' => 'general', 'description' => 'Koordinasi jadwal dan detail acara dengan semua pihak'],
            ['days_before' => 14, 'title' => 'Pelunasan semua vendor', 'category' => 'general', 'description' => 'Pastikan semua pembayaran vendor sudah lunas'],
            ['days_before' => 7, 'title' => 'Gladi bersih / rehearsal', 'category' => 'general', 'description' => 'Latihan prosesi dan cek kelengkapan'],
            ['days_before' => 3, 'title' => 'Prepare emergency kit', 'category' => 'general', 'description' => 'Siapkan: P3K, sewing kit, safety pin, tisu, snack'],
            ['days_before' => 1, 'title' => 'Final check semua persiapan', 'category' => 'general', 'description' => 'Pastikan semua berjalan sesuai rencana'],
            ['days_before' => 0, 'title' => '✨ Hari Pernikahan!', 'category' => 'general', 'description' => 'Selamat menempuh hidup baru! Nikmati setiap momennya 💕'],
        ];

        if ($concept === 'mewah') {
            $extra = [
                ['days_before' => 250, 'title' => 'Booking wedding planner profesional', 'category' => 'general', 'description' => 'Hire wedding planner/organizer berpengalaman'],
                ['days_before' => 190, 'title' => 'Pre-wedding photoshoot', 'category' => 'foto', 'description' => 'Sesi foto pre-wedding'],
                ['days_before' => 160, 'title' => 'Desain seating arrangement', 'category' => 'general', 'description' => 'Susun denah meja tamu VIP'],
                ['days_before' => 100, 'title' => 'Pesan kue pengantin custom', 'category' => 'catering', 'description' => 'Desain dan pesan wedding cake custom'],
            ];
            $base = array_merge($base, $extra);
        }

        if ($concept === 'intimate') {
            $extra = [
                ['days_before' => 180, 'title' => 'Pilih intimate venue (restoran/villa)', 'category' => 'venue', 'description' => 'Venue kecil yang nyaman untuk jumlah tamu terbatas'],
                ['days_before' => 100, 'title' => 'Personal touch: tulis surat untuk tamu', 'category' => 'undangan', 'description' => 'Siapkan surat personal untuk setiap tamu'],
            ];
            $base = array_merge($base, $extra);
        }

        if ($concept === 'outdoor') {
            $extra = [
                ['days_before' => 300, 'title' => 'Cek lokasi outdoor + plan B cuaca', 'category' => 'venue', 'description' => 'Pastikan ada tenda/indoor backup jika hujan'],
                ['days_before' => 200, 'title' => 'Siapkan penerangan outdoor', 'category' => 'dekor', 'description' => 'Lampu hias, lampion, fairy lights untuk malam hari'],
                ['days_before' => 7, 'title' => 'Cek prakiraan cuaca', 'category' => 'general', 'description' => 'Monitor cuaca dan aktifkan plan B jika perlu'],
            ];
            $base = array_merge($base, $extra);
        }

        // Sort by days_before descending
        usort($base, fn($a, $b) => $b['days_before'] <=> $a['days_before']);

        return $base;
    }

    /**
     * Get budget percentage distribution based on concept.
     */
    private function getBudgetPercentages(string $concept): array
    {
        $templates = [
            'simple' => [
                ['name' => 'Venue / Gedung', 'percent' => 30, 'icon' => 'fa-building', 'color' => '#6366f1'],
                ['name' => 'Catering', 'percent' => 35, 'icon' => 'fa-utensils', 'color' => '#f59e0b'],
                ['name' => 'Dekorasi', 'percent' => 8, 'icon' => 'fa-palette', 'color' => '#ec4899'],
                ['name' => 'Foto & Video', 'percent' => 12, 'icon' => 'fa-camera', 'color' => '#10b981'],
                ['name' => 'Busana & MUA', 'percent' => 5, 'icon' => 'fa-shirt', 'color' => '#8b5cf6'],
                ['name' => 'Undangan', 'percent' => 2, 'icon' => 'fa-envelope', 'color' => '#06b6d4'],
                ['name' => 'Souvenir', 'percent' => 3, 'icon' => 'fa-gift', 'color' => '#f43f5e'],
                ['name' => 'Lainnya', 'percent' => 5, 'icon' => 'fa-ellipsis', 'color' => '#64748b'],
            ],
            'mewah' => [
                ['name' => 'Venue / Gedung', 'percent' => 25, 'icon' => 'fa-building', 'color' => '#6366f1'],
                ['name' => 'Catering', 'percent' => 22, 'icon' => 'fa-utensils', 'color' => '#f59e0b'],
                ['name' => 'Dekorasi', 'percent' => 18, 'icon' => 'fa-palette', 'color' => '#ec4899'],
                ['name' => 'Foto & Video', 'percent' => 12, 'icon' => 'fa-camera', 'color' => '#10b981'],
                ['name' => 'Busana & MUA', 'percent' => 8, 'icon' => 'fa-shirt', 'color' => '#8b5cf6'],
                ['name' => 'Entertainment', 'percent' => 5, 'icon' => 'fa-music', 'color' => '#a855f7'],
                ['name' => 'Undangan', 'percent' => 2, 'icon' => 'fa-envelope', 'color' => '#06b6d4'],
                ['name' => 'Souvenir', 'percent' => 3, 'icon' => 'fa-gift', 'color' => '#f43f5e'],
                ['name' => 'Lainnya', 'percent' => 5, 'icon' => 'fa-ellipsis', 'color' => '#64748b'],
            ],
            'intimate' => [
                ['name' => 'Venue / Restoran', 'percent' => 22, 'icon' => 'fa-building', 'color' => '#6366f1'],
                ['name' => 'Catering', 'percent' => 35, 'icon' => 'fa-utensils', 'color' => '#f59e0b'],
                ['name' => 'Dekorasi', 'percent' => 13, 'icon' => 'fa-palette', 'color' => '#ec4899'],
                ['name' => 'Foto & Video', 'percent' => 15, 'icon' => 'fa-camera', 'color' => '#10b981'],
                ['name' => 'Busana & MUA', 'percent' => 7, 'icon' => 'fa-shirt', 'color' => '#8b5cf6'],
                ['name' => 'Undangan', 'percent' => 2, 'icon' => 'fa-envelope', 'color' => '#06b6d4'],
                ['name' => 'Souvenir', 'percent' => 3, 'icon' => 'fa-gift', 'color' => '#f43f5e'],
                ['name' => 'Lainnya', 'percent' => 3, 'icon' => 'fa-ellipsis', 'color' => '#64748b'],
            ],
            'outdoor' => [
                ['name' => 'Venue Outdoor', 'percent' => 20, 'icon' => 'fa-tree', 'color' => '#6366f1'],
                ['name' => 'Catering', 'percent' => 30, 'icon' => 'fa-utensils', 'color' => '#f59e0b'],
                ['name' => 'Dekorasi & Tenda', 'percent' => 18, 'icon' => 'fa-palette', 'color' => '#ec4899'],
                ['name' => 'Foto & Video', 'percent' => 12, 'icon' => 'fa-camera', 'color' => '#10b981'],
                ['name' => 'Busana & MUA', 'percent' => 6, 'icon' => 'fa-shirt', 'color' => '#8b5cf6'],
                ['name' => 'Entertainment', 'percent' => 4, 'icon' => 'fa-music', 'color' => '#a855f7'],
                ['name' => 'Undangan', 'percent' => 2, 'icon' => 'fa-envelope', 'color' => '#06b6d4'],
                ['name' => 'Souvenir', 'percent' => 3, 'icon' => 'fa-gift', 'color' => '#f43f5e'],
                ['name' => 'Lainnya', 'percent' => 5, 'icon' => 'fa-ellipsis', 'color' => '#64748b'],
            ],
        ];

        return $templates[$concept] ?? $templates['simple'];
    }
}
