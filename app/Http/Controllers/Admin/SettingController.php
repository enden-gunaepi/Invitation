<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $groupMap = [
            'app_name' => 'general',
            'app_domain' => 'general',
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

        foreach ($request->except('_token', '_method') as $key => $value) {
            Setting::set($key, $value, $groupMap[$key] ?? 'general');
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan berhasil disimpan!');
    }
}
