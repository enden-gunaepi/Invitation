<?php

namespace App\Http\Controllers\Client\Planner;

use App\Http\Controllers\Controller;
use App\Models\Planner\WpBudgetCategory;
use App\Models\Planner\WpBudgetItem;
use App\Models\Planner\WpProfile;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index()
    {
        $profile = $this->getProfile();
        $categories = $profile->budgetCategories()->with('items')->orderBy('sort_order')->get();

        $summary = [
            'total_budget' => (float) $profile->total_budget,
            'total_estimated' => (float) $categories->sum('estimated_amount'),
            'total_actual' => (float) $categories->sum('actual_amount'),
            'remaining' => max(0, (float) $profile->total_budget - (float) $categories->sum('actual_amount')),
            'percent' => $profile->total_budget > 0
                ? (int) round(($categories->sum('actual_amount') / (float) $profile->total_budget) * 100)
                : 0,
        ];

        $summary['status'] = match (true) {
            $summary['percent'] > 100 => 'danger',
            $summary['percent'] > 80 => 'warning',
            default => 'safe',
        };

        $overBudgetCategories = $categories->filter(fn ($c) => $c->isOverBudget());

        return view('client.planner.budget.index', compact('profile', 'categories', 'summary', 'overBudgetCategories'));
    }

    public function storeCategory(Request $request)
    {
        $profile = $this->getProfile();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'estimated_amount' => 'required|numeric|min:0',
            'icon' => 'nullable|string|max:30',
            'color' => 'nullable|string|max:20',
        ]);

        $maxSort = $profile->budgetCategories()->max('sort_order') ?? 0;

        WpBudgetCategory::create([
            'wp_profile_id' => $profile->id,
            'name' => $validated['name'],
            'estimated_amount' => $validated['estimated_amount'],
            'sort_order' => $maxSort + 1,
            'icon' => $validated['icon'] ?? 'fa-tag',
            'color' => $validated['color'] ?? '#6366f1',
        ]);

        return back()->with('success', 'Kategori budget ditambahkan!');
    }

    public function storeItem(Request $request)
    {
        $validated = $request->validate([
            'wp_budget_category_id' => 'required|exists:wp_budget_categories,id',
            'name' => 'required|string|max:255',
            'vendor_name' => 'nullable|string|max:255',
            'estimated_amount' => 'nullable|numeric|min:0',
            'actual_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $category = WpBudgetCategory::findOrFail($validated['wp_budget_category_id']);
        $this->authorizeCategory($category);

        WpBudgetItem::create($validated);
        $category->recalculate();

        return back()->with('success', 'Item budget ditambahkan!');
    }

    public function updateItem(Request $request, WpBudgetItem $item)
    {
        $this->authorizeCategory($item->category);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'vendor_name' => 'nullable|string|max:255',
            'estimated_amount' => 'nullable|numeric|min:0',
            'actual_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'paid_at' => 'nullable|date',
        ]);

        $item->update($validated);
        $item->category->recalculate();

        return back()->with('success', 'Item budget diupdate!');
    }

    public function destroyItem(WpBudgetItem $item)
    {
        $category = $item->category;
        $this->authorizeCategory($category);
        $item->delete();
        $category->recalculate();
        return back()->with('success', 'Item dihapus.');
    }

    public function destroyCategory(WpBudgetCategory $category)
    {
        $this->authorizeCategory($category);
        $category->delete();
        return back()->with('success', 'Kategori dihapus.');
    }

    private function getProfile(): WpProfile
    {
        $profile = WpProfile::where('user_id', auth()->id())->first();
        if (!$profile || !$profile->onboarding_completed) {
            abort(redirect()->route('client.planner.onboarding.step1'));
        }
        return $profile;
    }

    private function authorizeCategory(WpBudgetCategory $category): void
    {
        if ($category->profile->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
