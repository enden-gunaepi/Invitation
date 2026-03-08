<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\XenditService;
use App\Services\TripayService;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{
    public function index()
    {
        $config = [
            'xendit_secret_key' => Setting::get('xendit_secret_key', ''),
            'xendit_callback_token' => Setting::get('xendit_callback_token', ''),
            'xendit_mode' => Setting::get('xendit_mode', 'sandbox'),
            'xendit_enabled' => Setting::get('xendit_enabled', '0'),
            'tripay_api_key' => Setting::get('tripay_api_key', ''),
            'tripay_private_key' => Setting::get('tripay_private_key', ''),
            'tripay_merchant_code' => Setting::get('tripay_merchant_code', ''),
            'tripay_mode' => Setting::get('tripay_mode', 'sandbox'),
            'tripay_enabled' => Setting::get('tripay_enabled', '0'),
        ];

        return view('admin.payment-gateway.index', compact('config'));
    }

    public function update(Request $request)
    {
        $keys = [
            'xendit_secret_key', 'xendit_callback_token', 'xendit_mode', 'xendit_enabled',
            'tripay_api_key', 'tripay_private_key', 'tripay_merchant_code', 'tripay_mode', 'tripay_enabled',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key), 'payment');
            }
        }

        // Handle unchecked checkboxes (not sent in form)
        if (!$request->has('xendit_enabled')) {
            Setting::set('xendit_enabled', '0', 'payment');
        }
        if (!$request->has('tripay_enabled')) {
            Setting::set('tripay_enabled', '0', 'payment');
        }

        return redirect()->route('admin.payment-gateway.index')
            ->with('success', 'Konfigurasi payment gateway berhasil disimpan!');
    }

    public function testConnection(Request $request)
    {
        $gateway = $request->input('gateway');

        if ($gateway === 'xendit') {
            $service = new XenditService();
            $result = $service->testConnection();
        } elseif ($gateway === 'tripay') {
            $service = new TripayService();
            $result = $service->testConnection();
        } else {
            return back()->with('error', 'Gateway tidak dikenal.');
        }

        if ($result['success']) {
            return back()->with('success', ucfirst($gateway) . ' berhasil terhubung! ✓');
        }

        return back()->with('error', ucfirst($gateway) . ' gagal: ' . ($result['error'] ?? 'Unknown error'));
    }
}
