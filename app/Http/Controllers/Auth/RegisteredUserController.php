<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AffiliateClick;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $rawRefCode = trim((string) request()->query('ref', ''));
        $refCode = strtoupper($rawRefCode);
        $referrer = null;

        if ($refCode !== '') {
            $referrer = User::where('referral_code', $refCode)->first();
        }

        return view('auth.register', compact('refCode', 'referrer'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'referral_code' => ['nullable', 'string', 'max:40'],
        ]);

        $referralCode = strtoupper(trim((string) $request->input('referral_code', '')));
        $referrer = null;
        if ($referralCode !== '') {
            $referrer = User::where('referral_code', $referralCode)->first();
            if (!$referrer) {
                throw ValidationException::withMessages([
                    'referral_code' => 'Kode referral tidak valid.',
                ]);
            }
        }

        // First registered user becomes Administrator
        $isFirstUser = User::count() === 0;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $isFirstUser ? 'admin' : 'client',
            'is_active' => true,
            'referred_by_user_id' => $referrer?->id,
            'signup_ip' => $request->ip(),
            'signup_ua_hash' => hash('sha256', (string) $request->userAgent()),
            'referral_clicked_at' => $request->session()->get('referral_click.clicked_at'),
        ]);

        $clickId = (int) $request->session()->get('referral_click.click_id', 0);
        if ($clickId > 0) {
            AffiliateClick::where('id', $clickId)
                ->whereNull('converted_user_id')
                ->update([
                    'converted_user_id' => $user->id,
                    'converted_at' => now(),
                ]);
            $request->session()->forget('referral_click');
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
