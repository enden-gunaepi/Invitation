<?php

namespace App\Http\Controllers;

use App\Models\AffiliateClick;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function visit(Request $request, string $referralCode): RedirectResponse
    {
        $code = strtoupper(trim($referralCode));
        $referrer = User::where('referral_code', $code)->first();

        if (!$referrer) {
            return redirect()->route('register')->with('error', 'Kode referral tidak valid.');
        }

        $uaHash = hash('sha256', (string) $request->userAgent());
        $click = AffiliateClick::create([
            'referrer_user_id' => $referrer->id,
            'referral_code' => $code,
            'ip_address' => $request->ip(),
            'ua_hash' => $uaHash,
            'landing_url' => (string) $request->fullUrl(),
        ]);

        $request->session()->put('referral_click', [
            'click_id' => $click->id,
            'code' => $code,
            'clicked_at' => now()->toDateTimeString(),
        ]);

        return redirect()->route('register', ['ref' => $code]);
    }
}
