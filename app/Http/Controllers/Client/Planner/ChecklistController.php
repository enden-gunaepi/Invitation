<?php

namespace App\Http\Controllers\Client\Planner;

use App\Http\Controllers\Controller;
use App\Models\Planner\WpChecklistItem;
use App\Models\Planner\WpProfile;
use Illuminate\Http\Request;

class ChecklistController extends Controller
{
    public function index(Request $request)
    {
        $profile = $this->getProfile();
        $query = $profile->checklistItems();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $items = $query->orderBy('sort_order')->get();
        $categories = $profile->checklistItems()->reorder()->distinct()->pluck('category')->sort()->values();


        $stats = [
            'total' => $profile->checklistItems()->count(),
            'todo' => $profile->checklistItems()->where('status', 'todo')->count(),
            'in_progress' => $profile->checklistItems()->where('status', 'in_progress')->count(),
            'done' => $profile->checklistItems()->where('status', 'done')->count(),
            'overdue' => $profile->checklistItems()
                ->where('status', '!=', 'done')
                ->whereNotNull('deadline')
                ->where('deadline', '<', now())
                ->count(),
        ];

        return view('client.planner.checklist.index', compact('profile', 'items', 'categories', 'stats'));
    }

    public function store(Request $request)
    {
        $profile = $this->getProfile();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'category' => 'required|string|max:50',
            'deadline' => 'nullable|date',
        ]);

        $maxSort = $profile->checklistItems()->max('sort_order') ?? 0;

        WpChecklistItem::create([
            'wp_profile_id' => $profile->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'],
            'deadline' => $validated['deadline'] ?? null,
            'status' => 'todo',
            'sort_order' => $maxSort + 1,
            'is_auto_generated' => false,
        ]);

        return back()->with('success', 'Checklist item berhasil ditambahkan!');
    }

    public function update(Request $request, WpChecklistItem $checklist)
    {
        $this->authorizeItem($checklist);

        $validated = $request->validate([
            'status' => 'sometimes|in:todo,in_progress,done',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:500',
            'deadline' => 'nullable|date',
        ]);

        if (isset($validated['status']) && $validated['status'] === 'done' && $checklist->status !== 'done') {
            $validated['completed_at'] = now();
        }

        if (isset($validated['status']) && $validated['status'] !== 'done') {
            $validated['completed_at'] = null;
        }

        $checklist->update($validated);

        return back()->with('success', 'Checklist updated!');
    }

    public function destroy(WpChecklistItem $checklist)
    {
        $this->authorizeItem($checklist);
        $checklist->delete();
        return back()->with('success', 'Item dihapus.');
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*' => 'integer|exists:wp_checklist_items,id',
        ]);

        $profile = $this->getProfile();

        foreach ($validated['items'] as $index => $id) {
            WpChecklistItem::where('id', $id)
                ->where('wp_profile_id', $profile->id)
                ->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    private function getProfile(): WpProfile
    {
        $profile = WpProfile::where('user_id', auth()->id())->first();
        if (!$profile || !$profile->onboarding_completed) {
            abort(redirect()->route('client.planner.onboarding.step1'));
        }
        return $profile;
    }

    private function authorizeItem(WpChecklistItem $item): void
    {
        if ($item->profile->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
