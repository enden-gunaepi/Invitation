<?php

namespace App\Http\Controllers\Client\Planner;

use App\Http\Controllers\Controller;
use App\Models\Planner\WpAdvisorLog;
use App\Models\Planner\WpProfile;
use App\Services\Planner\WeddingAdvisorService;
use Illuminate\Http\Request;

class AdvisorController extends Controller
{
    public function __construct(
        private readonly WeddingAdvisorService $advisorService,
    ) {}

    public function index()
    {
        $profile = $this->getProfile();
        $healthScore = $this->advisorService->calculateHealthScore($profile);
        $recentLogs = $profile->advisorLogs()->take(10)->get();

        return view('client.planner.advisor.index', compact('profile', 'healthScore', 'recentLogs'));
    }

    public function ask(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:500',
        ]);

        $profile = $this->getProfile();
        $result = $this->advisorService->answerQuestion($profile, $request->input('question'));

        WpAdvisorLog::create([
            'wp_profile_id' => $profile->id,
            'question' => $request->input('question'),
            'answer' => $result['answer'],
            'category' => $result['category'],
        ]);

        return back()->with('advisor_answer', $result['answer']);
    }

    private function getProfile(): WpProfile
    {
        $profile = WpProfile::where('user_id', auth()->id())->first();
        if (!$profile || !$profile->onboarding_completed) {
            abort(redirect()->route('client.planner.onboarding.step1'));
        }
        return $profile;
    }
}
