<?php

namespace App\Http\Middleware;

use App\Services\ClientPackageService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveClientPackage
{
    public function __construct(
        private readonly ClientPackageService $clientPackageService,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        [$canCreate, $message] = $this->clientPackageService->canCreateInvitation((int) auth()->id());
        if (!$canCreate) {
            return redirect()
                ->route('client.packages.select')
                ->with('error', $message ?? 'Pilih paket aktif terlebih dahulu.');
        }

        return $next($request);
    }
}

