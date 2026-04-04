<?php

namespace App\Http\Controllers\Client\Planner;

use App\Http\Controllers\Controller;
use App\Models\Planner\WpProfile;
use App\Models\Rsvp;
use App\Services\Planner\WeddingAdvisorService;

class DashboardController extends Controller
{
    public function __construct(
        private readonly WeddingAdvisorService $advisorService,
    ) {}

    public function index()
    {
        $profile = WpProfile::where('user_id', auth()->id())->first();

        if (!$profile || !$profile->onboarding_completed) {
            return redirect()->route('client.planner.onboarding.step1');
        }

        $profile->load(['checklistItems', 'budgetCategories.items', 'vendors', 'timelineEvents']);

        // Health score
        $healthScore = $this->advisorService->calculateHealthScore($profile);

        // Urgent tasks (deadline within 7 days)
        $urgentTasks = $profile->checklistItems()
            ->where('status', '!=', 'done')
            ->whereNotNull('deadline')
            ->where('deadline', '<=', now()->addDays(7))
            ->orderBy('deadline')
            ->take(5)
            ->get();

        // Overdue tasks
        $overdueTasks = $profile->checklistItems()
            ->where('status', '!=', 'done')
            ->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->count();

        // Budget summary
        $budgetSummary = [
            'total' => (float) $profile->total_budget,
            'used' => $profile->budget_used,
            'remaining' => $profile->budget_remaining,
            'percent' => $profile->budget_percent,
        ];

        // Checklist progress
        $checklistTotal = $profile->checklistItems->count();
        $checklistDone = $profile->checklistItems->where('status', 'done')->count();
        $checklistProgress = $checklistTotal > 0 ? (int) round(($checklistDone / $checklistTotal) * 100) : 0;

        // Vendor summary
        $vendorTotal = $profile->vendors->count();
        $vendorSecured = $profile->vendors->whereIn('status', ['deal', 'dp_paid', 'lunas'])->count();
        $vendorPaymentDue = $profile->vendors->filter(fn ($v) => $v->isPaymentDueSoon())->count();

        // RSVP integration
        $rsvpData = null;
        if ($profile->invitation_id) {
            $attending = Rsvp::where('invitation_id', $profile->invitation_id)
                ->where('status', 'attending')->sum('pax');
            $totalRsvp = Rsvp::where('invitation_id', $profile->invitation_id)->count();
            $rsvpData = [
                'total' => $totalRsvp,
                'attending' => (int) $attending,
                'estimasi_porsi' => (int) ceil($attending * 1.1),
            ];
        }

        return view('client.planner.dashboard', compact(
            'profile',
            'healthScore',
            'urgentTasks',
            'overdueTasks',
            'budgetSummary',
            'checklistProgress',
            'checklistTotal',
            'checklistDone',
            'vendorTotal',
            'vendorSecured',
            'vendorPaymentDue',
            'rsvpData',
        ));
    }
}
