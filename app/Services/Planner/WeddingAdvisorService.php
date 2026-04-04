<?php

namespace App\Services\Planner;

use App\Models\Planner\WpProfile;
use App\Models\Rsvp;

class WeddingAdvisorService
{
    /**
     * Calculate the overall Wedding Health Score (0-100).
     */
    public function calculateHealthScore(WpProfile $profile): array
    {
        $checklist = $this->checklistScore($profile);
        $budget = $this->budgetScore($profile);
        $vendor = $this->vendorScore($profile);
        $timeline = $this->timelineScore($profile);

        $overall = (int) round(
            ($checklist['score'] * 0.30)
            + ($budget['score'] * 0.30)
            + ($vendor['score'] * 0.20)
            + ($timeline['score'] * 0.20)
        );

        return [
            'overall' => min(100, max(0, $overall)),
            'label' => $this->scoreLabel($overall),
            'color' => $this->scoreColor($overall),
            'dimensions' => [
                'checklist' => $checklist,
                'budget' => $budget,
                'vendor' => $vendor,
                'timeline' => $timeline,
            ],
        ];
    }

    /**
     * Answer a user question using rule-based logic.
     */
    public function answerQuestion(WpProfile $profile, string $question): array
    {
        $q = mb_strtolower(trim($question));
        $category = 'general';

        // Budget-related questions
        if ($this->matchesKeywords($q, ['budget', 'biaya', 'cukup', 'uang', 'harga', 'mahal', 'murah', 'hemat'])) {
            $category = 'budget';
            $answer = $this->budgetAdvice($profile, $q);
        }
        // Vendor-related questions
        elseif ($this->matchesKeywords($q, ['vendor', 'booking', 'pesan', 'cari', 'catering', 'fotografer', 'dekorasi', 'makeup', 'mc'])) {
            $category = 'vendor';
            $answer = $this->vendorAdvice($profile, $q);
        }
        // Timeline-related questions
        elseif ($this->matchesKeywords($q, ['kapan', 'timeline', 'jadwal', 'waktu', 'berapa lama', 'duluan', 'prioritas'])) {
            $category = 'timeline';
            $answer = $this->timelineAdvice($profile, $q);
        }
        // Guest-related questions
        elseif ($this->matchesKeywords($q, ['tamu', 'undang', 'rsvp', 'kursi', 'meja', 'konsumsi', 'porsi'])) {
            $category = 'guest';
            $answer = $this->guestAdvice($profile, $q);
        }
        // General
        else {
            $answer = $this->generalAdvice($profile, $q);
        }

        return [
            'answer' => $answer,
            'category' => $category,
        ];
    }

    // ─── Score Calculations ────────────────────────────

    private function checklistScore(WpProfile $profile): array
    {
        $total = $profile->checklistItems()->count();
        if ($total === 0) return ['score' => 50, 'label' => 'Belum ada checklist'];

        $done = $profile->checklistItems()->where('status', 'done')->count();
        $overdue = $profile->checklistItems()
            ->where('status', '!=', 'done')
            ->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->count();

        $score = (int) round(($done / $total) * 100);
        $score = max(0, $score - ($overdue * 5)); // Penalty for overdue

        return [
            'score' => min(100, max(0, $score)),
            'label' => "{$done}/{$total} selesai" . ($overdue > 0 ? ", {$overdue} terlambat" : ''),
            'done' => $done,
            'total' => $total,
            'overdue' => $overdue,
        ];
    }

    private function budgetScore(WpProfile $profile): array
    {
        $totalBudget = (float) $profile->total_budget;
        if ($totalBudget <= 0) return ['score' => 50, 'label' => 'Budget belum diatur'];

        $used = (float) $profile->budget_used;
        $percent = ($used / $totalBudget) * 100;

        if ($percent <= 80) {
            $score = 100;
            $label = 'Aman (' . number_format($percent, 0) . '% terpakai)';
        } elseif ($percent <= 100) {
            $score = 100 - (int) round(($percent - 80) * 2.5);
            $label = 'Hati-hati (' . number_format($percent, 0) . '% terpakai)';
        } else {
            $over = $percent - 100;
            $score = max(0, 50 - (int) round($over * 2));
            $label = 'Over budget ' . number_format($over, 0) . '%!';
        }

        return [
            'score' => min(100, max(0, $score)),
            'label' => $label,
            'percent' => (int) round($percent),
        ];
    }

    private function vendorScore(WpProfile $profile): array
    {
        $total = $profile->vendors()->count();
        if ($total === 0) return ['score' => 30, 'label' => 'Belum ada vendor'];

        $secured = $profile->vendors()->whereIn('status', ['deal', 'dp_paid', 'lunas'])->count();
        $lunas = $profile->vendors()->where('status', 'lunas')->count();

        $score = (int) round(($secured / $total) * 60 + ($lunas / $total) * 40);

        return [
            'score' => min(100, max(0, $score)),
            'label' => "{$secured}/{$total} vendor deal",
            'secured' => $secured,
            'lunas' => $lunas,
            'total' => $total,
        ];
    }

    private function timelineScore(WpProfile $profile): array
    {
        $total = $profile->timelineEvents()->count();
        if ($total === 0) return ['score' => 50, 'label' => 'Belum ada timeline'];

        $completed = $profile->timelineEvents()->where('is_completed', true)->count();
        $overdue = $profile->timelineEvents()
            ->where('is_completed', false)
            ->whereNotNull('target_date')
            ->where('target_date', '<', now())
            ->count();

        $score = (int) round(($completed / $total) * 100);
        $score = max(0, $score - ($overdue * 5));

        return [
            'score' => min(100, max(0, $score)),
            'label' => "{$completed}/{$total} milestone tercapai",
            'completed' => $completed,
            'total' => $total,
        ];
    }

    // ─── Advisor Answers ───────────────────────────────

    private function budgetAdvice(WpProfile $profile, string $q): string
    {
        $budget = (float) $profile->total_budget;
        $guests = (int) $profile->target_guests;
        $city = $profile->city ?? 'Indonesia';
        $concept = $profile->concept;

        $perGuest = $guests > 0 ? $budget / $guests : 0;
        $budgetFormatted = 'Rp' . number_format($budget, 0, ',', '.');
        $perGuestFormatted = 'Rp' . number_format($perGuest, 0, ',', '.');

        $lines = [];
        $lines[] = "📊 **Analisis Budget Kamu:**";
        $lines[] = "- Total budget: **{$budgetFormatted}**";
        $lines[] = "- Target tamu: **{$guests} orang**";
        $lines[] = "- Budget per tamu: **{$perGuestFormatted}**";
        $lines[] = "";

        if ($perGuest < 150000) {
            $lines[] = "⚠️ Budget per tamu cukup ketat ({$perGuestFormatted}). Untuk konsep **{$concept}**, disarankan minimal Rp150.000-250.000/tamu.";
            $lines[] = "";
            $lines[] = "💡 **Tips hemat:**";
            $lines[] = "- Gunakan **undangan digital** (sudah tersedia di platform ini!) — hemat Rp3-5 juta";
            $lines[] = "- Pilih venue yang sudah include catering (paket all-in)";
            $lines[] = "- Kurangi jumlah tamu atau pilih konsep **intimate**";
        } elseif ($perGuest < 300000) {
            $lines[] = "✅ Budget cukup untuk konsep **{$concept}** di {$city}, tapi perlu dikelola dengan ketat.";
            $lines[] = "";
            $lines[] = "💡 **Tips:**";
            $lines[] = "- Prioritaskan vendor yang sudah include banyak item (paket all-in)";
            $lines[] = "- Manfaatkan **undangan digital** untuk hemat biaya undangan cetak";
        } else {
            $lines[] = "✅ Budget sangat memadai ({$perGuestFormatted}/tamu) untuk konsep **{$concept}** di {$city}!";
            $lines[] = "";
            $lines[] = "💡 **Saran:**";
            $lines[] = "- Alokasikan budget lebih untuk dekorasi & entertainment biar memorable";
            $lines[] = "- Pertimbangkan pre-wedding photoshoot professional";
        }

        // Check actual spending
        $used = (float) $profile->budget_used;
        if ($used > 0) {
            $usedPercent = round(($used / $budget) * 100);
            $remaining = $budget - $used;
            $lines[] = "";
            $lines[] = "📈 **Status saat ini:** {$usedPercent}% budget terpakai, sisa Rp" . number_format($remaining, 0, ',', '.');
            if ($usedPercent > 80) {
                $lines[] = "⚠️ Budget sudah hampir habis! Review pengeluaran di menu Budget Tracker.";
            }
        }

        return implode("\n", $lines);
    }

    private function vendorAdvice(WpProfile $profile, string $q): string
    {
        $daysLeft = $profile->days_remaining ?? 365;

        $lines = [];
        $lines[] = "🧾 **Prioritas Booking Vendor:**";
        $lines[] = "";

        if ($daysLeft > 270) {
            $lines[] = "Dengan H-" . round($daysLeft / 30) . " bulan, prioritas kamu sekarang:";
            $lines[] = "1. 🏛️ **Venue** — Ini yang paling cepat penuh, booking ASAP!";
            $lines[] = "2. 🍽️ **Catering** — Lakukan food tasting di 3-5 vendor";
            $lines[] = "3. 📸 **Fotografer** — Fotografer bagus juga cepat full";
        } elseif ($daysLeft > 180) {
            $lines[] = "Dengan H-" . round($daysLeft / 30) . " bulan lagi:";
            $lines[] = "1. 🎨 **Dekorasi** — Konsultasi konsep & warna";
            $lines[] = "2. 👗 **Gaun/Jas** — Mulai fitting pertama";
            $lines[] = "3. 💄 **MUA** — Book dan trial makeup";
        } elseif ($daysLeft > 90) {
            $lines[] = "H-" . round($daysLeft / 30) . " bulan — fase finalisasi:";
            $lines[] = "1. 🎤 **MC & Entertainment** — Booking segera";
            $lines[] = "2. 💌 **Undangan Digital** — Buat dan kirim sekarang!";
            $lines[] = "3. 🎁 **Souvenir** — Pesan sesuai estimasi tamu";
        } else {
            $lines[] = "⚡ Tinggal H-{$daysLeft} hari! Fokus pada:";
            $lines[] = "1. ✅ Pelunasan semua vendor";
            $lines[] = "2. 📋 Briefing & koordinasi final";
            $lines[] = "3. 🔄 Rehearsal / gladi bersih";
        }

        // Show current vendor status
        $vendorCount = $profile->vendors()->count();
        if ($vendorCount > 0) {
            $secured = $profile->vendors()->whereIn('status', ['deal', 'dp_paid', 'lunas'])->count();
            $lines[] = "";
            $lines[] = "📊 Status vendor kamu: **{$secured}/{$vendorCount}** sudah deal";
        }

        return implode("\n", $lines);
    }

    private function timelineAdvice(WpProfile $profile, string $q): string
    {
        $daysLeft = $profile->days_remaining;
        $lines = [];

        if ($daysLeft === null) {
            return "⚠️ Tanggal pernikahan belum diisi. Silakan update di profil planner kamu.";
        }

        $months = round($daysLeft / 30);
        $lines[] = "📆 **Timeline Kamu: H-{$daysLeft} hari (±{$months} bulan)**";
        $lines[] = "";

        // Get uncompleted tasks with deadline soonest
        $urgentTasks = $profile->checklistItems()
            ->where('status', '!=', 'done')
            ->whereNotNull('deadline')
            ->orderBy('deadline')
            ->take(5)
            ->get();

        if ($urgentTasks->isNotEmpty()) {
            $lines[] = "📌 **Yang harus kamu kerjakan sekarang:**";
            foreach ($urgentTasks as $task) {
                $diff = now()->diffInDays($task->deadline, false);
                $emoji = $diff < 0 ? '🔴' : ($diff <= 7 ? '🟡' : '🟢');
                $label = $diff < 0 ? '(terlambat ' . abs($diff) . ' hari!)' : "(H-{$diff} hari)";
                $lines[] = "- {$emoji} **{$task->title}** {$label}";
            }
        } else {
            $lines[] = "✅ Semua task saat ini sudah selesai! Great job! 🎉";
        }

        return implode("\n", $lines);
    }

    private function guestAdvice(WpProfile $profile, string $q): string
    {
        $target = (int) $profile->target_guests;
        $budget = (float) $profile->total_budget;

        $lines = [];
        $lines[] = "👥 **Analisis Tamu:**";
        $lines[] = "- Target undangan: **{$target} orang**";

        // Estimate from RSVP if invitation exists
        if ($profile->invitation_id) {
            $attending = Rsvp::where('invitation_id', $profile->invitation_id)
                ->where('status', 'attending')
                ->sum('pax');
            $totalRsvp = Rsvp::where('invitation_id', $profile->invitation_id)->count();

            $lines[] = "- RSVP masuk: **{$totalRsvp}** (hadir: **{$attending} pax**)";
            $lines[] = "";

            if ($attending > 0) {
                $estimasiPorsi = (int) ceil($attending * 1.1); // 10% buffer
                $cateringBudget = $budget * 0.30; // ~30% budget
                $perPorsi = $cateringBudget / max(1, $estimasiPorsi);
                $lines[] = "🍽️ **Estimasi konsumsi:**";
                $lines[] = "- Estimasi porsi (+ 10% buffer): **{$estimasiPorsi} porsi**";
                $lines[] = "- Budget catering/orang: **Rp" . number_format($perPorsi, 0, ',', '.') . "**";
            }
        }

        $lines[] = "";
        $lines[] = "💡 **Tips:**";
        $lines[] = "- Biasanya 70-80% dari undangan akan hadir";
        $lines[] = "- Siapkan porsi cadangan 5-10%";
        $lines[] = "- Semakin cepat kirim undangan, semakin akurat RSVP";

        return implode("\n", $lines);
    }

    private function generalAdvice(WpProfile $profile, string $q): string
    {
        $score = $this->calculateHealthScore($profile);
        $daysLeft = $profile->days_remaining ?? 365;

        $lines = [];
        $lines[] = "💍 **Status Persiapan Pernikahan Kamu:**";
        $lines[] = "";
        $lines[] = "- 📊 Health Score: **{$score['overall']}/100** ({$score['label']})";
        $lines[] = "- 📆 Sisa waktu: **{$daysLeft} hari**";
        $lines[] = "- ✅ Checklist: {$score['dimensions']['checklist']['label']}";
        $lines[] = "- 💰 Budget: {$score['dimensions']['budget']['label']}";
        $lines[] = "- 🧾 Vendor: {$score['dimensions']['vendor']['label']}";
        $lines[] = "";
        $lines[] = "💡 Kamu bisa tanya tentang:";
        $lines[] = "- **Budget** → \"Budget 50jt cukup gak?\"";
        $lines[] = "- **Vendor** → \"Vendor apa yang harus di-booking duluan?\"";
        $lines[] = "- **Timeline** → \"Hari ini harus ngapain?\"";
        $lines[] = "- **Tamu** → \"Estimasi konsumsi berapa porsi?\"";

        return implode("\n", $lines);
    }

    // ─── Helpers ────────────────────────────────────────

    private function matchesKeywords(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }
        return false;
    }

    private function scoreLabel(int $score): string
    {
        return match (true) {
            $score >= 90 => '💎 Perfect',
            $score >= 75 => '⭐ Great',
            $score >= 55 => '👍 Good',
            $score >= 35 => '⚠️ Perlu Perhatian',
            default => '🔴 Butuh Tindakan',
        };
    }

    private function scoreColor(int $score): string
    {
        return match (true) {
            $score >= 75 => '#10b981',
            $score >= 55 => '#f59e0b',
            default => '#ef4444',
        };
    }
}
