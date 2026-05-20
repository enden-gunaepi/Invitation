<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Template extends Model
{
    public const RENDER_MODE_BLADE = 'blade';
    public const RENDER_MODE_BUILDER = 'builder';

    protected $fillable = [
        'name', 'slug', 'category', 'thumbnail', 'preview_url', 'html_path',
        'color_schemes', 'is_premium', 'is_active', 'render_mode',
        'builder_config', 'builder_layout', 'schema_version',
    ];

    protected function casts(): array
    {
        return [
            'color_schemes' => 'array',
            'builder_config' => 'array',
            'is_premium' => 'boolean',
            'is_active' => 'boolean',
            'schema_version' => 'integer',
        ];
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function isBuilder(): bool
    {
        return $this->render_mode === self::RENDER_MODE_BUILDER;
    }

    public function isBlade(): bool
    {
        return !$this->isBuilder();
    }

    public function resolvedBuilderConfig(): array
    {
        return self::normalizeBuilderConfig($this->builder_config, $this->category);
    }

    public static function renderModes(): array
    {
        return [
            self::RENDER_MODE_BLADE => 'Legacy Blade',
            self::RENDER_MODE_BUILDER => 'Builder CMS',
        ];
    }

    public static function builderLayouts(): array
    {
        return [
            'classic-romance' => 'Classic Romance',
            'modern-editorial' => 'Modern Editorial',
            'soft-garden' => 'Soft Garden',
            'gnv2-signature' => 'GNV2 Signature',
        ];
    }

    public static function builderFonts(): array
    {
        return [
            'playfair' => 'Playfair Display',
            'cormorant' => 'Cormorant Garamond',
            'lora' => 'Lora',
            'inter' => 'Inter',
            'jakarta' => 'Plus Jakarta Sans',
            'manrope' => 'Manrope',
        ];
    }

    public static function builderSpacingOptions(): array
    {
        return [
            'compact' => 'Compact',
            'comfortable' => 'Comfortable',
            'airy' => 'Airy',
        ];
    }

    public static function builderRadiusOptions(): array
    {
        return [
            'soft' => 'Soft',
            'rounded' => 'Rounded',
            'pill' => 'Pill',
        ];
    }

    public static function builderSectionCatalog(): array
    {
        return [
            'hero' => [
                'label' => 'Hero Cover',
                'variants' => [
                    'cover-centered' => 'Cover Centered',
                    'cover-split' => 'Cover Split',
                ],
                'default_variant' => 'cover-centered',
            ],
            'couple' => [
                'label' => 'Couple Intro',
                'variants' => [
                    'portrait-stack' => 'Portrait Stack',
                    'side-by-side' => 'Side by Side',
                ],
                'default_variant' => 'portrait-stack',
            ],
            'event_schedule' => [
                'label' => 'Event Schedule',
                'variants' => [
                    'cards' => 'Cards',
                    'timeline' => 'Timeline',
                ],
                'default_variant' => 'cards',
            ],
            'gallery' => [
                'label' => 'Gallery',
                'variants' => [
                    'mosaic' => 'Mosaic',
                    'grid' => 'Grid',
                ],
                'default_variant' => 'mosaic',
            ],
            'love_story' => [
                'label' => 'Love Story',
                'variants' => [
                    'timeline' => 'Timeline',
                    'cards' => 'Cards',
                ],
                'default_variant' => 'timeline',
            ],
            'gift' => [
                'label' => 'Gift',
                'variants' => [
                    'cards' => 'Cards',
                    'minimal' => 'Minimal',
                ],
                'default_variant' => 'cards',
            ],
            'rsvp' => [
                'label' => 'RSVP',
                'variants' => [
                    'panel' => 'Panel',
                    'split' => 'Split',
                ],
                'default_variant' => 'panel',
            ],
            'wishes' => [
                'label' => 'Wishes',
                'variants' => [
                    'feed' => 'Feed',
                    'cards' => 'Cards',
                ],
                'default_variant' => 'feed',
            ],
            'map' => [
                'label' => 'Map',
                'variants' => [
                    'card' => 'Card',
                    'split' => 'Split',
                ],
                'default_variant' => 'card',
            ],
            'footer' => [
                'label' => 'Footer',
                'variants' => [
                    'simple' => 'Simple',
                    'signature' => 'Signature',
                ],
                'default_variant' => 'simple',
            ],
        ];
    }

    public static function defaultBuilderConfig(?string $category = 'wedding'): array
    {
        $sections = [];

        foreach (self::builderSectionCatalog() as $key => $meta) {
            $sections[] = [
                'key' => $key,
                'enabled' => true,
                'variant' => $meta['default_variant'],
                'settings' => [],
            ];
        }

        return [
            'theme' => [
                'primary' => '#B76E79',
                'secondary' => '#FFF8F3',
                'accent' => '#7A4E57',
                'background' => '#FFFDFB',
                'text' => '#2B1F24',
                'heading_font' => 'playfair',
                'body_font' => 'jakarta',
                'spacing' => 'comfortable',
                'radius' => 'rounded',
            ],
            'sections' => $sections,
            'content_rules' => [
                'supported_event_types' => [$category ?: 'wedding'],
                'features' => [
                    'gallery' => true,
                    'love_story' => true,
                    'gift' => true,
                    'livestream' => false,
                ],
            ],
        ];
    }

    public static function normalizeBuilderConfig(?array $config, ?string $category = 'wedding'): array
    {
        $defaults = self::defaultBuilderConfig($category);
        $config = is_array($config) ? $config : [];

        $theme = array_merge($defaults['theme'], Arr::only((array) ($config['theme'] ?? []), array_keys($defaults['theme'])));

        foreach (['primary', 'secondary', 'accent', 'background', 'text'] as $colorKey) {
            $theme[$colorKey] = self::normalizeColor($theme[$colorKey], $defaults['theme'][$colorKey]);
        }

        if (!array_key_exists($theme['heading_font'], self::builderFonts())) {
            $theme['heading_font'] = $defaults['theme']['heading_font'];
        }

        if (!array_key_exists($theme['body_font'], self::builderFonts())) {
            $theme['body_font'] = $defaults['theme']['body_font'];
        }

        if (!array_key_exists($theme['spacing'], self::builderSpacingOptions())) {
            $theme['spacing'] = $defaults['theme']['spacing'];
        }

        if (!array_key_exists($theme['radius'], self::builderRadiusOptions())) {
            $theme['radius'] = $defaults['theme']['radius'];
        }

        $catalog = self::builderSectionCatalog();
        $inputSections = collect((array) ($config['sections'] ?? []))
            ->filter(fn ($section) => is_array($section) && isset($section['key']))
            ->keyBy(fn ($section) => (string) $section['key']);

        $sections = collect($catalog)->map(function (array $meta, string $key) use ($inputSections) {
            $section = (array) ($inputSections->get($key) ?? []);
            $variant = (string) ($section['variant'] ?? $meta['default_variant']);
            if (!array_key_exists($variant, $meta['variants'])) {
                $variant = $meta['default_variant'];
            }

            return [
                'key' => $key,
                'enabled' => array_key_exists('enabled', $section) ? (bool) $section['enabled'] : true,
                'variant' => $variant,
                'settings' => is_array($section['settings'] ?? null) ? $section['settings'] : [],
                'sort_order' => (int) ($section['sort_order'] ?? 999),
            ];
        })->sortBy('sort_order')->values()->map(function (array $section) {
            unset($section['sort_order']);
            return $section;
        })->all();

        $contentRules = (array) ($config['content_rules'] ?? []);
        $supportedTypes = collect((array) ($contentRules['supported_event_types'] ?? [$category ?: 'wedding']))
            ->map(fn ($type) => trim((string) $type))
            ->filter(fn ($type) => in_array($type, ['wedding', 'birthday', 'graduation', 'corporate', 'other'], true))
            ->values()
            ->all();

        if ($supportedTypes === []) {
            $supportedTypes = [$category ?: 'wedding'];
        }

        $features = array_merge(
            $defaults['content_rules']['features'],
            array_map('boolval', (array) ($contentRules['features'] ?? []))
        );

        return [
            'theme' => $theme,
            'sections' => $sections,
            'content_rules' => [
                'supported_event_types' => $supportedTypes,
                'features' => $features,
            ],
        ];
    }

    private static function normalizeColor(mixed $value, string $fallback): string
    {
        $value = trim((string) $value);

        if (preg_match('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value) === 1) {
            return Str::upper($value);
        }

        return $fallback;
    }
}
