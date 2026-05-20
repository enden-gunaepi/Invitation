@php
    $selectedMode = old('render_mode', $template->render_mode ?? \App\Models\Template::RENDER_MODE_BLADE);
    $config = old('render_mode')
        ? \App\Models\Template::normalizeBuilderConfig([
            'theme' => [
                'primary' => old('theme_primary'),
                'secondary' => old('theme_secondary'),
                'accent' => old('theme_accent'),
                'background' => old('theme_background'),
                'text' => old('theme_text'),
                'heading_font' => old('theme_heading_font'),
                'body_font' => old('theme_body_font'),
                'spacing' => old('theme_spacing'),
                'radius' => old('theme_radius'),
            ],
            'sections' => collect($sectionCatalog)->map(function ($meta, $key) {
                return [
                    'key' => $key,
                    'enabled' => old("section_enabled.$key") ? true : false,
                    'variant' => old("section_variant.$key", $meta['default_variant']),
                    'sort_order' => (int) old("section_order.$key", 0),
                    'settings' => [],
                ];
            })->values()->all(),
            'content_rules' => [
                'supported_event_types' => old('supported_event_types', [old('category', $template->category ?? 'wedding')]),
            ],
        ], old('category', $template->category ?? 'wedding'))
        : ($template?->resolvedBuilderConfig() ?? $defaultBuilderConfig);
@endphp

<div class="space-y-6">
    <div class="mb-5">
        <label class="form-label">Nama Template</label>
        <input type="text" name="name" value="{{ old('name', $template->name ?? '') }}" class="form-input" required>
        @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="form-label">Kategori</label>
            <select name="category" class="form-input">
                @foreach(['wedding', 'birthday', 'graduation', 'corporate', 'other'] as $cat)
                    <option value="{{ $cat }}" {{ old('category', $template->category ?? 'wedding') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Tipe Template</label>
            <select name="render_mode" class="form-input" id="renderModeSelect">
                @foreach($renderModes as $mode => $label)
                    <option value="{{ $mode }}" {{ $selectedMode === $mode ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('render_mode') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="form-label">Thumbnail</label>
            <input type="file" name="thumbnail" class="form-input" accept="image/*">
            @if(!empty($template?->thumbnail))
                <p class="text-xs text-slate-500 mt-1">Sudah ada: {{ $template->thumbnail }}</p>
            @endif
            @error('thumbnail') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex items-end gap-6 pb-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_premium" value="1" {{ old('is_premium', $template->is_premium ?? false) ? 'checked' : '' }} class="w-4 h-4 rounded bg-slate-800 border-slate-600 text-indigo-500">
                <span class="text-sm text-slate-400">Premium</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $template->is_active ?? true) ? 'checked' : '' }} class="w-4 h-4 rounded bg-slate-800 border-slate-600 text-indigo-500">
                <span class="text-sm text-slate-400">Aktif</span>
            </label>
        </div>
    </div>

    <div id="bladeFields" class="card" style="{{ $selectedMode === \App\Models\Template::RENDER_MODE_BLADE ? '' : 'display:none;' }}">
        <div class="mb-4">
            <h3 class="font-semibold text-base">Legacy Blade</h3>
            <p class="text-sm text-slate-500 mt-1">Mode ini mendaftarkan template Blade yang sudah dibuat developer.</p>
        </div>
        <label class="form-label">HTML Path</label>
        <input type="text" name="html_path" value="{{ old('html_path', $template->html_path ?? '') }}" class="form-input" placeholder="invitations.templates.wedding-peach.index">
        <p class="text-xs text-slate-500 mt-2">Wajib mengarah ke Blade view yang benar-benar ada di project.</p>
        @error('html_path') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div id="builderFields" class="space-y-5" style="{{ $selectedMode === \App\Models\Template::RENDER_MODE_BUILDER ? '' : 'display:none;' }}">
        <div class="card">
            <div class="mb-4">
                <h3 class="font-semibold text-base">Builder CMS</h3>
                <p class="text-sm text-slate-500 mt-1">Mode ini membuat template baru dari layout universal dan susunan section terstruktur.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Base Layout</label>
                    <select name="builder_layout" class="form-input">
                        <option value="">Pilih layout</option>
                        @foreach($builderLayouts as $key => $label)
                            <option value="{{ $key }}" {{ old('builder_layout', $template->builder_layout ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('builder_layout') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Event Type Yang Didukung</label>
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        @foreach(['wedding', 'birthday', 'graduation', 'corporate', 'other'] as $eventType)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="supported_event_types[]" value="{{ $eventType }}"
                                    {{ in_array($eventType, old('supported_event_types', $config['content_rules']['supported_event_types'] ?? []), true) ? 'checked' : '' }}>
                                <span>{{ ucfirst($eventType) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="mb-4">
                <h3 class="font-semibold text-base">Theme Tokens</h3>
                <p class="text-sm text-slate-500 mt-1">Atur warna dan tipografi dasar untuk seluruh tampilan builder.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                @foreach([
                    'theme_primary' => 'Primary',
                    'theme_secondary' => 'Secondary',
                    'theme_accent' => 'Accent',
                    'theme_background' => 'Background',
                    'theme_text' => 'Text',
                ] as $field => $label)
                    @php
                        $themeKey = str_replace('theme_', '', $field);
                    @endphp
                    <label>
                        <span class="form-label">{{ $label }}</span>
                        <input type="color" name="{{ $field }}" value="{{ old($field, $config['theme'][$themeKey] ?? '#000000') }}" class="form-input h-12 p-2">
                        @error($field) <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </label>
                @endforeach
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mt-5">
                <div>
                    <label class="form-label">Heading Font</label>
                    <select name="theme_heading_font" class="form-input">
                        @foreach($builderFonts as $key => $label)
                            <option value="{{ $key }}" {{ old('theme_heading_font', $config['theme']['heading_font'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Body Font</label>
                    <select name="theme_body_font" class="form-input">
                        @foreach($builderFonts as $key => $label)
                            <option value="{{ $key }}" {{ old('theme_body_font', $config['theme']['body_font'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Spacing</label>
                    <select name="theme_spacing" class="form-input">
                        @foreach($spacingOptions as $key => $label)
                            <option value="{{ $key }}" {{ old('theme_spacing', $config['theme']['spacing'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Corner Radius</label>
                    <select name="theme_radius" class="form-input">
                        @foreach($radiusOptions as $key => $label)
                            <option value="{{ $key }}" {{ old('theme_radius', $config['theme']['radius'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="mb-4">
                <h3 class="font-semibold text-base">Section Builder</h3>
                <p class="text-sm text-slate-500 mt-1">Tentukan section aktif, urutan tampil, dan variant layout tiap section.</p>
            </div>
            <div class="space-y-3">
                @foreach($sectionCatalog as $key => $meta)
                    @php
                        $existingSection = collect($config['sections'] ?? [])->firstWhere('key', $key) ?? [];
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-[1.2fr_0.8fr_120px] gap-4 items-center rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                        <label class="flex items-center gap-3">
                            <input type="checkbox" name="section_enabled[{{ $key }}]" value="1" {{ old("section_enabled.$key", $existingSection['enabled'] ?? true) ? 'checked' : '' }}>
                            <div>
                                <div class="font-medium">{{ $meta['label'] }}</div>
                                <div class="text-xs text-slate-500">{{ $key }}</div>
                            </div>
                        </label>
                        <div>
                            <label class="form-label">Variant</label>
                            <select name="section_variant[{{ $key }}]" class="form-input">
                                @foreach($meta['variants'] as $variantKey => $variantLabel)
                                    <option value="{{ $variantKey }}" {{ old("section_variant.$key", $existingSection['variant'] ?? $meta['default_variant']) === $variantKey ? 'selected' : '' }}>{{ $variantLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Urutan</label>
                            <input type="number" name="section_order[{{ $key }}]" class="form-input" min="1" value="{{ old("section_order.$key", collect($config['sections'] ?? [])->search(fn ($section) => ($section['key'] ?? null) === $key) + 1) }}">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary text-sm"><i class="fas fa-save mr-2"></i>{{ isset($template) ? 'Update' : 'Simpan' }}</button>
        <a href="{{ route('admin.templates.index') }}" class="btn-secondary text-sm">Batal</a>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const renderModeSelect = document.getElementById('renderModeSelect');
        const bladeFields = document.getElementById('bladeFields');
        const builderFields = document.getElementById('builderFields');

        if (!renderModeSelect || !bladeFields || !builderFields) {
            return;
        }

        const syncTemplateMode = () => {
            const isBuilder = renderModeSelect.value === '{{ \App\Models\Template::RENDER_MODE_BUILDER }}';
            bladeFields.style.display = isBuilder ? 'none' : '';
            builderFields.style.display = isBuilder ? '' : 'none';
        };

        renderModeSelect.addEventListener('change', syncTemplateMode);
        syncTemplateMode();
    });
</script>
@endpush
