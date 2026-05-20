@php
    use Illuminate\Support\Str;

    $templateConfig = $templateConfig ?? ($template?->resolvedBuilderConfig() ?? \App\Models\Template::defaultBuilderConfig($invitation->event_type ?? 'wedding'));
    $theme = $templateConfig['theme'] ?? \App\Models\Template::defaultBuilderConfig($invitation->event_type ?? 'wedding')['theme'];
    $sections = collect($templateConfig['sections'] ?? []);
    $demoMode = $demoMode ?? false;
    $layout = $template->builder_layout ?? 'classic-romance';
    $isGnv2 = $layout === 'gnv2-signature';

    $assetUrl = function (?string $path): ?string {
        if (empty($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    };

    $fontMap = [
        'playfair' => "'Playfair Display', serif",
        'cormorant' => "'Cormorant Garamond', serif",
        'lora' => "'Lora', serif",
        'inter' => "'Inter', sans-serif",
        'jakarta' => "'Plus Jakarta Sans', sans-serif",
        'manrope' => "'Manrope', sans-serif",
    ];

    $spacingMap = [
        'compact' => ['section' => '56px', 'container' => '900px'],
        'comfortable' => ['section' => '80px', 'container' => '1024px'],
        'airy' => ['section' => '104px', 'container' => '1120px'],
    ];

    $radiusMap = [
        'soft' => '18px',
        'rounded' => '28px',
        'pill' => '999px',
    ];

    $spacing = $spacingMap[$theme['spacing'] ?? 'comfortable'] ?? $spacingMap['comfortable'];
    $radius = $radiusMap[$theme['radius'] ?? 'rounded'] ?? $radiusMap['rounded'];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invitation->title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=Lora:wght@400;500;600;700&family=Manrope:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --builder-primary: {{ $isGnv2 ? '#9FBFD6' : ($theme['primary'] ?? '#B76E79') }};
            --builder-secondary: {{ $isGnv2 ? '#9FBFD6' : ($theme['secondary'] ?? '#FFF8F3') }};
            --builder-accent: {{ $isGnv2 ? '#DCECF8' : ($theme['accent'] ?? '#7A4E57') }};
            --builder-background: {{ $isGnv2 ? '#000000' : ($theme['background'] ?? '#FFFDFB') }};
            --builder-text: {{ $isGnv2 ? '#FFFFFF' : ($theme['text'] ?? '#2B1F24') }};
            --builder-radius: {{ $radius }};
            --builder-section-space: {{ $spacing['section'] }};
            --builder-container: {{ $spacing['container'] }};
            --builder-heading-font: {!! $fontMap[$theme['heading_font'] ?? 'playfair'] ?? "'Playfair Display', serif" !!};
            --builder-body-font: {!! $fontMap[$theme['body_font'] ?? 'jakarta'] ?? "'Plus Jakarta Sans', sans-serif" !!};
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: var(--builder-body-font);
            background:
                @if($isGnv2)
                radial-gradient(circle at top left, rgba(159, 191, 214, 0.12), transparent 22%),
                radial-gradient(circle at bottom right, rgba(159, 191, 214, 0.08), transparent 18%),
                #000;
                @else
                radial-gradient(circle at top right, rgba(183, 110, 121, 0.16), transparent 24%),
                radial-gradient(circle at bottom left, rgba(122, 78, 87, 0.12), transparent 18%),
                var(--builder-background);
                @endif
            color: var(--builder-text);
            line-height: 1.6;
            overflow-x: hidden;
        }
        img { max-width: 100%; display: block; }
        a { color: inherit; }
        .builder-shell { min-height: 100vh; padding: {{ $isGnv2 ? '0' : '24px 16px 72px' }}; }
        .builder-container { width: min(100%, var(--builder-container)); margin: 0 auto; }
        .builder-section { margin-top: {{ $isGnv2 ? '0' : 'var(--builder-section-space)' }}; }
        .builder-card {
            background: {{ $isGnv2 ? 'linear-gradient(135deg, rgba(255, 255, 255, 0.24), rgba(255, 255, 255, 0.08))' : 'rgba(255, 255, 255, 0.72)' }};
            backdrop-filter: blur(12px);
            border: 1px solid {{ $isGnv2 ? 'rgba(255,255,255,0.28)' : 'rgba(122, 78, 87, 0.12)' }};
            border-radius: var(--builder-radius);
            box-shadow: {{ $isGnv2 ? '0 20px 45px rgba(0, 0, 0, 0.28)' : '0 16px 50px rgba(70, 43, 50, 0.08)' }};
        }
        .builder-heading {
            font-family: var(--builder-heading-font);
            letter-spacing: -0.03em;
            margin: 0;
        }
        .builder-kicker {
            text-transform: uppercase;
            letter-spacing: 0.16em;
            font-size: 11px;
            color: var(--builder-accent);
            font-weight: 700;
            margin-bottom: 12px;
        }
        .builder-gnv2-panel {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 56px 20px;
            overflow: hidden;
            text-align: center;
        }
        .builder-gnv2-frame {
            position: absolute;
            inset: 0;
        }
        .builder-gnv2-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            animation: builderBgZoom 18s ease-in-out infinite alternate;
        }
        .builder-gnv2-overlay-dark {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
        }
        .builder-gnv2-overlay-blue {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(159,191,214,0.16), rgba(0,0,0,0.72));
        }
        .builder-gnv2-bottom-fade {
            position: absolute;
            inset-inline: 0;
            bottom: 0;
            height: 46%;
            background: linear-gradient(to top, rgba(0,0,0,0.95), rgba(0,0,0,0.38), transparent);
        }
        .builder-gnv2-content {
            position: relative;
            z-index: 1;
            width: min(100%, 760px);
        }
        .builder-gnv2-ornament {
            position: absolute;
            width: 120px;
            height: 120px;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 999px;
            opacity: 0.28;
            filter: blur(0.2px);
        }
        .builder-gnv2-ornament::before,
        .builder-gnv2-ornament::after {
            content: "";
            position: absolute;
            inset: 14px;
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 999px;
        }
        .builder-gnv2-ornament::after {
            inset: 30px;
        }
        .builder-gnv2-section-blue {
            background: #9fbfd6;
            color: #ffffff;
        }
        .builder-gnv2-grid {
            display: grid;
            gap: 20px;
        }
        .builder-gnv2-form {
            text-align: left;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.03));
            border: 1px solid rgba(255,255,255,0.22);
            border-radius: 18px;
            padding: 16px 16px 14px;
            backdrop-filter: blur(8px);
        }
        .builder-gnv2-form label {
            display: block;
            margin-bottom: 14px;
        }
        .builder-gnv2-form span {
            display: block;
            font-family: var(--builder-heading-font);
            font-size: 1.06rem;
            margin-bottom: 8px;
        }
        .builder-gnv2-input,
        .builder-gnv2-textarea,
        .builder-gnv2-select {
            width: 100%;
            border: 0;
            border-bottom: 1px solid rgba(255,255,255,0.58);
            background: transparent;
            color: #fff;
            padding: 9px 2px 10px;
            outline: none;
            border-radius: 0;
        }
        .builder-gnv2-input::placeholder,
        .builder-gnv2-textarea::placeholder {
            color: rgba(255,255,255,0.7);
        }
        .builder-gnv2-textarea {
            min-height: 94px;
            resize: vertical;
        }
        .builder-gnv2-select option {
            color: #111827;
            background: #fff;
        }
        .builder-gnv2-submit {
            width: 100%;
            border: 1px solid rgba(255,255,255,0.22);
            border-radius: 10px;
            padding: 12px 14px;
            background: rgba(23, 38, 54, 0.8);
            color: #fff;
            font-weight: 700;
        }
        .builder-gnv2-message-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.22), rgba(255,255,255,0.12));
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 16px;
            padding: 14px 16px;
            box-shadow: 0 10px 24px rgba(0,0,0,0.18);
            text-align: left;
        }
        .builder-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 22px;
        }
        .builder-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 18px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            border: 1px solid transparent;
        }
        .builder-button-primary {
            background: var(--builder-primary);
            color: white;
        }
        .builder-button-secondary {
            background: transparent;
            color: var(--builder-accent);
            border-color: rgba(122, 78, 87, 0.2);
        }
        .builder-grid {
            display: grid;
            gap: 22px;
        }
        .builder-empty {
            display: none;
        }
        @keyframes builderBgZoom {
            from { transform: scale(1); }
            to { transform: scale(1.06); }
        }
        @media (min-width: 768px) {
            .builder-shell { padding: {{ $isGnv2 ? '0' : '28px 24px 96px' }}; }
            .builder-grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .builder-grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .builder-gnv2-grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
    </style>
</head>
<body>
    <div class="builder-shell">
        <div class="builder-container">
            @foreach ($sections as $section)
                @continue(empty($section['enabled']))
                @if (in_array($section['key'] ?? '', array_keys(\App\Models\Template::builderSectionCatalog()), true))
                    @includeIf('invitations.builder.sections.' . $section['key'], [
                        'section' => $section,
                        'invitation' => $invitation,
                        'guest' => $guest ?? null,
                        'personalization' => $personalization ?? null,
                        'demoMode' => $demoMode,
                        'assetUrl' => $assetUrl,
                    ])
                @endif
            @endforeach
        </div>
    </div>
</body>
</html>
