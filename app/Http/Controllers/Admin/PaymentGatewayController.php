<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\TripayService;
use App\Services\XenditService;
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
            'payment_primary_gateway' => Setting::get('payment_primary_gateway', 'xendit'),
            'payment_expiry_seconds' => Setting::get('payment_expiry_seconds', '86400'),
            'payment_dev_mode' => Setting::get('payment_dev_mode', '0'),
            'payment_allow_qris' => Setting::get('payment_allow_qris', '1'),
            'payment_allow_ewallet' => Setting::get('payment_allow_ewallet', '1'),
            'payment_discount_enabled' => Setting::get('payment_discount_enabled', '0'),
            'payment_discount_type' => Setting::get('payment_discount_type', 'percent'),
            'payment_discount_value' => Setting::get('payment_discount_value', '0'),
            'payment_ppn_enabled' => Setting::get('payment_ppn_enabled', '0'),
            'payment_ppn_percent' => Setting::get('payment_ppn_percent', '11'),
        ];

        return view('admin.payment-gateway.index', compact('config'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'payment_primary_gateway' => 'required|in:xendit,tripay',
            'payment_expiry_seconds' => 'required|integer|min:1800|max:172800',
            'payment_discount_type' => 'nullable|in:percent,fixed',
            'payment_discount_value' => 'nullable|numeric|min:0',
            'payment_ppn_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($request->input('xendit_enabled') === '1') {
            if (blank($request->input('xendit_secret_key'))) {
                return back()->withInput()->with('error', 'Xendit tidak bisa diaktifkan tanpa Secret API Key.');
            }
            if ($request->input('xendit_mode') === 'production' && blank($request->input('xendit_callback_token'))) {
                return back()->withInput()->with('error', 'Xendit production membutuhkan callback verification token.');
            }
        }

        if ($request->input('tripay_enabled') === '1') {
            if (blank($request->input('tripay_api_key')) || blank($request->input('tripay_private_key')) || blank($request->input('tripay_merchant_code'))) {
                return back()->withInput()->with('error', 'Tripay tidak bisa diaktifkan tanpa API Key, Private Key, dan Merchant Code.');
            }
        }

        $keys = [
            'xendit_secret_key', 'xendit_callback_token', 'xendit_mode', 'xendit_enabled',
            'tripay_api_key', 'tripay_private_key', 'tripay_merchant_code', 'tripay_mode', 'tripay_enabled',
            'payment_primary_gateway', 'payment_expiry_seconds',
            'payment_dev_mode',
            'payment_allow_qris', 'payment_allow_ewallet',
            'payment_discount_enabled', 'payment_discount_type', 'payment_discount_value',
            'payment_ppn_enabled', 'payment_ppn_percent',
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
        if (!$request->has('payment_dev_mode')) {
            Setting::set('payment_dev_mode', '0', 'payment');
        }
        if (!$request->has('payment_allow_qris')) {
            Setting::set('payment_allow_qris', '0', 'payment');
        }
        if (!$request->has('payment_allow_ewallet')) {
            Setting::set('payment_allow_ewallet', '0', 'payment');
        }
        if (!$request->has('payment_discount_enabled')) {
            Setting::set('payment_discount_enabled', '0', 'payment');
        }
        if (!$request->has('payment_ppn_enabled')) {
            Setting::set('payment_ppn_enabled', '0', 'payment');
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
