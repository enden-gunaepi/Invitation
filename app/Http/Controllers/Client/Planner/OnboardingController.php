<?php

namespace App\Http\Controllers\Client\Planner;

use App\Http\Controllers\Controller;
use App\Models\Planner\WpProfile;
use App\Services\Planner\PlannerOnboardingService;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function __construct(
        private readonly PlannerOnboardingService $onboardingService,
    ) {}

    public function showStep1()
    {
        $profile = $this->getOrCreateProfile();

        if ($profile->onboarding_completed) {
            return redirect()->route('client.planner.dashboard');
        }

        return view('client.planner.onboarding.step1', compact('profile'));
    }

    public function processStep1(Request $request)
    {
        $validated = $request->validate([
            'partner_1_name' => 'required|string|max:255',
            'partner_2_name' => 'required|string|max:255',
            'wedding_date' => 'required|date|after:today',
            'city' => 'required|string|max:100',
        ]);

        $profile = $this->getOrCreateProfile();
        $profile->update($validated);

        return redirect()->route('client.planner.onboarding.step2');
    }

    public function showStep2()
    {
        $profile = $this->getOrCreateProfile();

        if ($profile->onboarding_completed) {
            return redirect()->route('client.planner.dashboard');
        }
        if (!$profile->wedding_date) {
            return redirect()->route('client.planner.onboarding.step1');
        }

        return view('client.planner.onboarding.step2', compact('profile'));
    }

    public function processStep2(Request $request)
    {
        $validated = $request->validate([
            'target_guests' => 'required|integer|min:10|max:5000',
            'concept' => 'required|in:simple,mewah,intimate,outdoor',
        ]);

        $profile = $this->getOrCreateProfile();
        $profile->update($validated);

        return redirect()->route('client.planner.onboarding.step3');
    }

    public function showStep3()
    {
        $profile = $this->getOrCreateProfile();

        if ($profile->onboarding_completed) {
            return redirect()->route('client.planner.dashboard');
        }
        if (!$profile->concept) {
            return redirect()->route('client.planner.onboarding.step2');
        }

        return view('client.planner.onboarding.step3', compact('profile'));
    }

    public function processStep3(Request $request)
    {
        $validated = $request->validate([
            'total_budget' => 'required|numeric|min:1000000',
        ]);

        $profile = $this->getOrCreateProfile();
        $profile->update([
            'total_budget' => $validated['total_budget'],
            'onboarding_completed' => true,
        ]);

        // Generate all initial data
        $this->onboardingService->generateInitialData($profile);

        return redirect()->route('client.planner.dashboard')
            ->with('success', '🎉 Wedding Planner kamu siap! Semua checklist, budget, dan timeline sudah digenerate otomatis.');
    }

    private function getOrCreateProfile(): WpProfile
    {
        return WpProfile::firstOrCreate(
            ['user_id' => auth()->id()],
            [
                'partner_1_name' => auth()->user()->name,
                'concept' => 'simple',
                'target_guests' => 100,
            ]
        );
    }
}
