<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Template::latest()->paginate(12);
        return view('admin.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.templates.create', $this->formViewData());
    }

    public function store(Request $request)
    {
        $validated = $this->validateTemplateInput($request);

        $validated['slug'] = $this->generateUniqueSlug($validated['name']);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('templates', 'public');
        }

        Template::create($validated);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template berhasil ditambahkan!');
    }

    public function edit(Template $template)
    {
        return view('admin.templates.edit', array_merge(
            ['template' => $template],
            $this->formViewData()
        ));
    }

    public function update(Request $request, Template $template)
    {
        $validated = $this->validateTemplateInput($request, $template);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('templates', 'public');
        }

        $template->update($validated);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template berhasil diupdate!');
    }

    public function destroy(Template $template)
    {
        $template->delete();

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template berhasil dihapus!');
    }

    private function validateTemplateInput(Request $request, ?Template $template = null): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|in:wedding,birthday,graduation,corporate,other',
            'render_mode' => ['required', Rule::in(array_keys(Template::renderModes()))],
            'thumbnail' => 'nullable|image|max:2048',
            'html_path' => 'nullable|string|max:255',
            'builder_layout' => ['nullable', Rule::in(array_keys(Template::builderLayouts()))],
            'theme_primary' => ['nullable', 'regex:/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'theme_secondary' => ['nullable', 'regex:/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'theme_accent' => ['nullable', 'regex:/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'theme_background' => ['nullable', 'regex:/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'theme_text' => ['nullable', 'regex:/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'theme_heading_font' => ['nullable', Rule::in(array_keys(Template::builderFonts()))],
            'theme_body_font' => ['nullable', Rule::in(array_keys(Template::builderFonts()))],
            'theme_spacing' => ['nullable', Rule::in(array_keys(Template::builderSpacingOptions()))],
            'theme_radius' => ['nullable', Rule::in(array_keys(Template::builderRadiusOptions()))],
            'supported_event_types' => 'nullable|array',
            'supported_event_types.*' => 'in:wedding,birthday,graduation,corporate,other',
            'section_enabled' => 'nullable|array',
            'section_variant' => 'nullable|array',
            'section_order' => 'nullable|array',
        ]);

        $validated['is_premium'] = $request->boolean('is_premium');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['schema_version'] = 1;

        if ($validated['render_mode'] === Template::RENDER_MODE_BLADE) {
            $validated['html_path'] = trim((string) ($validated['html_path'] ?? ''));

            if ($validated['html_path'] === '') {
                throw ValidationException::withMessages([
                    'html_path' => 'HTML Path wajib diisi untuk template Legacy Blade.',
                ]);
            }

            if (!view()->exists($validated['html_path'])) {
                throw ValidationException::withMessages([
                    'html_path' => 'Blade view untuk HTML Path tersebut tidak ditemukan.',
                ]);
            }

            $validated['builder_layout'] = null;
            $validated['builder_config'] = null;

            return $validated;
        }

        if (empty($validated['builder_layout'])) {
            throw ValidationException::withMessages([
                'builder_layout' => 'Base layout wajib dipilih untuk template Builder CMS.',
            ]);
        }

        $validated['html_path'] = null;
        $validated['builder_config'] = $this->buildBuilderConfigFromRequest($request, $validated['category']);

        return $validated;
    }

    private function buildBuilderConfigFromRequest(Request $request, string $category): array
    {
        $sections = [];
        $catalog = Template::builderSectionCatalog();

        foreach ($catalog as $key => $meta) {
            $sections[] = [
                'key' => $key,
                'enabled' => $request->boolean("section_enabled.{$key}"),
                'variant' => (string) $request->input("section_variant.{$key}", $meta['default_variant']),
                'sort_order' => (int) $request->input("section_order.{$key}", 0),
                'settings' => [],
            ];
        }

        $enabledSections = collect($sections)
            ->filter(fn (array $section) => $section['enabled'])
            ->pluck('key');

        return Template::normalizeBuilderConfig([
            'theme' => [
                'primary' => $request->input('theme_primary'),
                'secondary' => $request->input('theme_secondary'),
                'accent' => $request->input('theme_accent'),
                'background' => $request->input('theme_background'),
                'text' => $request->input('theme_text'),
                'heading_font' => $request->input('theme_heading_font'),
                'body_font' => $request->input('theme_body_font'),
                'spacing' => $request->input('theme_spacing'),
                'radius' => $request->input('theme_radius'),
            ],
            'sections' => $sections,
            'content_rules' => [
                'supported_event_types' => $request->input('supported_event_types', [$category]),
                'features' => [
                    'gallery' => $enabledSections->contains('gallery'),
                    'love_story' => $enabledSections->contains('love_story'),
                    'gift' => $enabledSections->contains('gift'),
                    'livestream' => true,
                ],
            ],
        ], $category);
    }

    private function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base !== '' ? $base : 'template';
        $counter = 2;

        while (Template::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function formViewData(): array
    {
        return [
            'renderModes' => Template::renderModes(),
            'builderLayouts' => Template::builderLayouts(),
            'builderFonts' => Template::builderFonts(),
            'spacingOptions' => Template::builderSpacingOptions(),
            'radiusOptions' => Template::builderRadiusOptions(),
            'sectionCatalog' => Template::builderSectionCatalog(),
            'defaultBuilderConfig' => Template::defaultBuilderConfig(),
        ];
    }
}
