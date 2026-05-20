<?php

namespace App\Services;

use App\Models\Template;

class TemplateRenderService
{
    private const FALLBACK_VIEW = 'invitations.templates.wedding-elegant.index';
    private const BUILDER_VIEW = 'invitations.builder.show';

    public function resolveView(?Template $template): string
    {
        if ($template?->isBuilder()) {
            return self::BUILDER_VIEW;
        }

        $htmlPath = trim((string) ($template?->html_path ?? ''));
        if ($htmlPath !== '' && view()->exists($htmlPath)) {
            return $htmlPath;
        }

        return self::FALLBACK_VIEW;
    }

    public function resolveData(?Template $template): array
    {
        if ($template?->isBuilder()) {
            return [
                'templateRenderMode' => Template::RENDER_MODE_BUILDER,
                'templateConfig' => $template->resolvedBuilderConfig(),
            ];
        }

        return [
            'templateRenderMode' => Template::RENDER_MODE_BLADE,
        ];
    }
}
