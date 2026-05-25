<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name' => ['nullable', 'string', 'max:255'],
            'app_domain' => ['nullable', 'string', 'max:255'],
            'company_logo' => ['nullable', 'image', 'max:4096'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_phone' => ['nullable', 'string', 'max:255'],
            'company_email' => ['nullable', 'email', 'max:255'],
            'company_address' => ['nullable', 'string'],
            'company_instagram' => ['nullable', 'string', 'max:255'],
            'company_facebook' => ['nullable', 'string', 'max:255'],
            'mail_from' => ['nullable', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],
            'whatsapp_mode' => ['nullable', 'string', 'max:50'],
            'whatsapp_phone_number_id' => ['nullable', 'string', 'max:255'],
            'whatsapp_api_token' => ['nullable', 'string'],
            'whatsapp_api_version' => ['nullable', 'string', 'max:50'],
            'whatsapp_base_url' => ['nullable', 'string', 'max:255'],
        ]);

        $groupMap = [
            'app_name' => 'general',
            'app_domain' => 'general',
            'company_logo' => 'company',
            'mail_from' => 'email',
            'mail_from_name' => 'email',
            'company_name' => 'company',
            'company_phone' => 'company',
            'company_email' => 'company',
            'company_address' => 'company',
            'company_instagram' => 'company',
            'company_facebook' => 'company',
            'whatsapp_mode' => 'integration',
            'whatsapp_phone_number_id' => 'integration',
            'whatsapp_api_token' => 'integration',
            'whatsapp_api_version' => 'integration',
            'whatsapp_base_url' => 'integration',
        ];

        if ($request->hasFile('company_logo')) {
            $oldLogo = Setting::get('company_logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            Setting::set('company_logo', $request->file('company_logo')->store('company_logos', 'public'), 'company');
        }

        foreach ($request->except('_token', '_method', 'company_logo') as $key => $value) {
            Setting::set($key, $value, $groupMap[$key] ?? 'general');
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan berhasil disimpan!');
    }
}
