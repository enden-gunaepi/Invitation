<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::latest()->paginate(10);
        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        $templates = Template::where('is_active', true)->orderBy('name')->get();
        return view('admin.packages.create', compact('templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'tier' => 'required|in:starter,growth,pro,enterprise',
            'description' => 'nullable|string',
            'badge_text' => 'nullable|string|max:60',
            'support_level' => 'nullable|string|max:80',
            'sla_hours' => 'nullable|integer|min:1|max:168',
            'price' => 'required|numeric|min:0',
            'billing_type' => 'required|in:one_time,subscription',
            'billing_cycle' => 'nullable|in:monthly,yearly',
            'max_guests' => 'required|integer|min:1',
            'max_photos' => 'required|integer|min:1',
            'max_invitations' => 'required|integer|min:1',
            'features_input' => 'nullable|string',
            'addons_input' => 'nullable|string',
            'allowed_template_ids' => 'nullable|array',
            'allowed_template_ids.*' => 'exists:templates,id',
            'is_active' => 'boolean',
            'is_recommended' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $validated['is_recommended'] = $request->has('is_recommended');
        $validated['features'] = $this->parseLinesToArray($request->input('features_input', ''));
        $validated['addons'] = $this->parseLinesToArray($request->input('addons_input', ''));
        unset($validated['features_input'], $validated['addons_input']);

        Package::create($validated);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Paket berhasil ditambahkan!');
    }

    public function edit(Package $package)
    {
        $templates = Template::where('is_active', true)->orderBy('name')->get();
        return view('admin.packages.edit', compact('package', 'templates'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'tier' => 'required|in:starter,growth,pro,enterprise',
            'description' => 'nullable|string',
            'badge_text' => 'nullable|string|max:60',
            'support_level' => 'nullable|string|max:80',
            'sla_hours' => 'nullable|integer|min:1|max:168',
            'price' => 'required|numeric|min:0',
            'billing_type' => 'required|in:one_time,subscription',
            'billing_cycle' => 'nullable|in:monthly,yearly',
            'max_guests' => 'required|integer|min:1',
            'max_photos' => 'required|integer|min:1',
            'max_invitations' => 'required|integer|min:1',
            'features_input' => 'nullable|string',
            'addons_input' => 'nullable|string',
            'allowed_template_ids' => 'nullable|array',
            'allowed_template_ids.*' => 'exists:templates,id',
            'is_active' => 'boolean',
            'is_recommended' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_recommended'] = $request->has('is_recommended');
        $validated['features'] = $this->parseLinesToArray($request->input('features_input', ''));
        $validated['addons'] = $this->parseLinesToArray($request->input('addons_input', ''));
        unset($validated['features_input'], $validated['addons_input']);

        $package->update($validated);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Paket berhasil diupdate!');
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()->route('admin.packages.index')
            ->with('success', 'Paket berhasil dihapus!');
    }

    private function parseLinesToArray(?string $input): array
    {
        if (!$input) {
            return [];
        }

        return collect(preg_split('/\r\n|\r|\n/', $input))
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }
}
