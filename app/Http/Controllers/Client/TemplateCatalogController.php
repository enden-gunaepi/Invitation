<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Services\ClientPackageService;

class TemplateCatalogController extends Controller
{
    public function __construct(
        private readonly ClientPackageService $clientPackageService,
    ) {
    }

    public function index()
    {
        $templates = Template::query()
            ->where('is_active', true)
            ->orderBy('is_premium', 'desc')
            ->orderBy('name')
            ->paginate(12);

        $hasActivePackage = (bool) $this->clientPackageService->getActiveSubscription((int) auth()->id());

        return view('client.templates.index', compact('templates', 'hasActivePackage'));
    }
}
