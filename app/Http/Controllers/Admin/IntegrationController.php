<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\TelegramService;
use App\Services\TripayService;
use App\Services\WeaGateService;
use App\Services\XenditService;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.integration.telegram');
    }

    public function telegram()
    {
        $config = [
            'telegram_bot_token'       => Setting::get('telegram_bot_token', ''),
            'telegram_chat_id'         => Setting::get('telegram_chat_id', ''),
            'telegram_notify_chat_id'  => Setting::get('telegram_notify_chat_id', ''),
            'telegram_enabled'         => Setting::get('telegram_enabled', '0'),
            'telegram_webhook_active'  => Setting::get('telegram_webhook_active', '0'),
        ];

        $webhookInfo = null;
        if (!empty($config['telegram_bot_token'])) {
            $service = new TelegramService();
            $result = $service->getWebhookInfo();
            if ($result['success']) {
                $webhookInfo = $result['data'];
            }
        }

        $webhookUrl = route('telegram.webhook');

        return view('admin.integration.telegram', compact('config', 'webhookInfo', 'webhookUrl'));
    }

    public function telegramUpdate(Request $request)
    {
        $request->validate([
            'telegram_bot_token' => 'nullable|string|max:255',
            'telegram_chat_id' => 'nullable|string|max:100',
        ]);

        $keys = ['telegram_bot_token', 'telegram_chat_id', 'telegram_notify_chat_id', 'telegram_enabled'];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key), 'integration');
            }
        }

        if (!$request->has('telegram_enabled')) {
            Setting::set('telegram_enabled', '0', 'integration');
        }

        return redirect()->route('admin.integration.telegram')
            ->with('success', 'Konfigurasi Telegram berhasil disimpan!');
    }

    public function telegramTestConnection()
    {
        $service = new TelegramService();
        $result = $service->testConnection();

        if ($result['success']) {
            return back()->with('success', "Bot terhubung! @{$result['bot_username']} ({$result['bot_name']})");
        }

        return back()->with('error', 'Koneksi gagal: ' . ($result['error'] ?? 'Unknown error'));
    }

    public function telegramSetWebhook()
    {
        $service = new TelegramService();
        $webhookUrl = route('telegram.webhook');

        $result = $service->setWebhook($webhookUrl);

        if ($result['success']) {
            Setting::set('telegram_webhook_active', '1', 'integration');
            return back()->with('success', 'Webhook berhasil di-set! URL: ' . $webhookUrl);
        }

        return back()->with('error', 'Set webhook gagal: ' . ($result['error'] ?? 'Unknown error'));
    }

    public function telegramDeleteWebhook()
    {
        $service = new TelegramService();
        $result = $service->deleteWebhook();

        if ($result['success']) {
            Setting::set('telegram_webhook_active', '0', 'integration');
            return back()->with('success', 'Webhook berhasil dihapus!');
        }

        return back()->with('error', 'Hapus webhook gagal: ' . ($result['error'] ?? 'Unknown error'));
    }

    public function telegramTestMessage()
    {
        $service = new TelegramService();
        $result = $service->sendMessage("✅ <b>Test Message</b>\n\nKoneksi Telegram berhasil! Pesan ini dikirim dari panel admin.");

        if ($result['success']) {
            return back()->with('success', 'Pesan test berhasil dikirim!');
        }

        return back()->with('error', 'Kirim pesan gagal: ' . ($result['error'] ?? 'Unknown error'));
    }

    public function whatsapp()
    {
        $config = [
            'whatsapp_vendor' => Setting::get('whatsapp_vendor', 'weagate'),
            'whatsapp_enabled' => Setting::get('whatsapp_enabled', '0'),
            'whatsapp_weagate_token' => Setting::get('whatsapp_weagate_token', ''),
            'whatsapp_weagate_instan' => Setting::get('whatsapp_weagate_instan', '1'),
            'whatsapp_weagate_domain_api' => Setting::get('whatsapp_weagate_domain_api', 'https://mywifi.weagate.com'),
        ];

        return view('admin.integration.whatsapp', compact('config'));
    }

    public function whatsappUpdate(Request $request)
    {
        $request->validate([
            'whatsapp_vendor' => 'required|string|in:weagate',
            'whatsapp_weagate_token' => 'nullable|string|max:500',
            'whatsapp_weagate_domain_api' => 'nullable|url|max:255',
        ]);

        $keys = [
            'whatsapp_vendor', 'whatsapp_enabled',
            'whatsapp_weagate_token', 'whatsapp_weagate_instan', 'whatsapp_weagate_domain_api',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key), 'integration');
            }
        }

        if (!$request->has('whatsapp_enabled')) {
            Setting::set('whatsapp_enabled', '0', 'integration');
        }
        if (!$request->has('whatsapp_weagate_instan')) {
            Setting::set('whatsapp_weagate_instan', '0', 'integration');
        }

        return redirect()->route('admin.integration.whatsapp')
            ->with('success', 'Konfigurasi WhatsApp berhasil disimpan!');
    }

    public function whatsappTestConnection()
    {
        $vendor = Setting::get('whatsapp_vendor', 'weagate');

        if ($vendor === 'weagate') {
            $service = new WeaGateService();
            $result = $service->testConnection();

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'device_name' => $result['device_name'],
                    'package' => $result['package'],
                    'device_status' => $result['device_status'],
                    'expired_at' => $result['expired_at'],
                    'domain' => $result['domain'],
                ]);
            }

            return response()->json(['success' => false, 'error' => $result['error'] ?? 'Unknown error']);
        }

        return response()->json(['success' => false, 'error' => 'Vendor tidak dikenal.']);
    }

    public function whatsappTestMessage(Request $request)
    {
        $request->validate([
            'test_phone' => 'required|string|max:20',
            'test_message' => 'nullable|string|max:1000',
        ]);

        $vendor = Setting::get('whatsapp_vendor', 'weagate');
        $phone = preg_replace('/[^0-9]/', '', $request->input('test_phone'));
        $message = $request->input('test_message', '✅ Test pesan dari panel admin. Integrasi WhatsApp berhasil!');

        if ($vendor === 'weagate') {
            $service = new WeaGateService();
            $result = $service->sendMessage($phone, $message);

            if ($result['success']) {
                return response()->json(['success' => true, 'phone' => $phone]);
            }

            return response()->json(['success' => false, 'error' => $result['error'] ?? 'Unknown error']);
        }

        return response()->json(['success' => false, 'error' => 'Vendor tidak dikenal.']);
    }

    public function email()
    {
        return view('admin.integration.email');
    }

    public function paymentGateway()
    {
        $config = [
            'xendit_secret_key'        => Setting::get('xendit_secret_key', ''),
            'xendit_callback_token'    => Setting::get('xendit_callback_token', ''),
            'xendit_mode'              => Setting::get('xendit_mode', 'sandbox'),
            'xendit_enabled'           => Setting::get('xendit_enabled', '0'),
            'tripay_api_key'           => Setting::get('tripay_api_key', ''),
            'tripay_private_key'       => Setting::get('tripay_private_key', ''),
            'tripay_merchant_code'     => Setting::get('tripay_merchant_code', ''),
            'tripay_mode'              => Setting::get('tripay_mode', 'sandbox'),
            'tripay_enabled'           => Setting::get('tripay_enabled', '0'),
            'payment_primary_gateway'  => Setting::get('payment_primary_gateway', 'xendit'),
            'payment_expiry_seconds'   => Setting::get('payment_expiry_seconds', '86400'),
            'payment_dev_mode'         => Setting::get('payment_dev_mode', '0'),
            'payment_allow_qris'       => Setting::get('payment_allow_qris', '1'),
            'payment_allow_ewallet'    => Setting::get('payment_allow_ewallet', '1'),
            'payment_discount_enabled' => Setting::get('payment_discount_enabled', '0'),
            'payment_discount_type'    => Setting::get('payment_discount_type', 'percent'),
            'payment_discount_value'   => Setting::get('payment_discount_value', '0'),
            'payment_ppn_enabled'      => Setting::get('payment_ppn_enabled', '0'),
            'payment_ppn_percent'      => Setting::get('payment_ppn_percent', '11'),
            // Metode pembayaran aktif
            'payment_method_gateway'          => Setting::get('payment_method_gateway', '1'),
            'payment_method_transfer_manual'  => Setting::get('payment_method_transfer_manual', '0'),
        ];

        return view('admin.integration.payment-gateway', compact('config'));
    }

    public function paymentGatewayUpdate(Request $request)
    {
        $request->validate([
            'payment_primary_gateway' => 'required|in:xendit,tripay',
            'payment_expiry_seconds'  => 'required|integer|min:1800|max:172800',
            'payment_discount_type'   => 'nullable|in:percent,fixed',
            'payment_discount_value'  => 'nullable|numeric|min:0',
            'payment_ppn_percent'     => 'nullable|numeric|min:0|max:100',
        ]);

        $vendor = $request->input('payment_primary_gateway');
        $isGatewayEnabled = $request->input('payment_method_gateway') === '1';

        if ($isGatewayEnabled) {
            if ($vendor === 'xendit') {
                if (blank($request->input('xendit_secret_key'))) {
                    return back()->withInput()->with('error', 'Xendit tidak bisa diaktifkan tanpa Secret API Key.');
                }
                if ($request->input('xendit_mode') === 'production' && blank($request->input('xendit_callback_token'))) {
                    return back()->withInput()->with('error', 'Xendit production membutuhkan callback verification token.');
                }
            }

            if ($vendor === 'tripay') {
                if (blank($request->input('tripay_api_key')) || blank($request->input('tripay_private_key')) || blank($request->input('tripay_merchant_code'))) {
                    return back()->withInput()->with('error', 'Tripay tidak bisa diaktifkan tanpa API Key, Private Key, dan Merchant Code.');
                }
            }
        }

        $keys = [
            'xendit_secret_key', 'xendit_callback_token', 'xendit_mode',
            'tripay_api_key', 'tripay_private_key', 'tripay_merchant_code', 'tripay_mode',
            'payment_primary_gateway', 'payment_expiry_seconds',
            'payment_dev_mode', 'payment_allow_qris', 'payment_allow_ewallet',
            'payment_discount_enabled', 'payment_discount_type', 'payment_discount_value',
            'payment_ppn_enabled', 'payment_ppn_percent',
            'payment_method_gateway', 'payment_method_transfer_manual',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key), 'payment');
            }
        }

        // Hanya satu vendor yang aktif sesuai pilihan
        Setting::set('xendit_enabled', $vendor === 'xendit' ? '1' : '0', 'payment');
        Setting::set('tripay_enabled', $vendor === 'tripay' ? '1' : '0', 'payment');

        if (!$request->has('payment_dev_mode'))         Setting::set('payment_dev_mode', '0', 'payment');
        if (!$request->has('payment_allow_qris'))       Setting::set('payment_allow_qris', '0', 'payment');
        if (!$request->has('payment_allow_ewallet'))    Setting::set('payment_allow_ewallet', '0', 'payment');
        if (!$request->has('payment_discount_enabled')) Setting::set('payment_discount_enabled', '0', 'payment');
        if (!$request->has('payment_ppn_enabled'))      Setting::set('payment_ppn_enabled', '0', 'payment');
        if (!$request->has('payment_method_gateway'))         Setting::set('payment_method_gateway', '0', 'payment');
        if (!$request->has('payment_method_transfer_manual')) Setting::set('payment_method_transfer_manual', '0', 'payment');

        // Minimal satu metode harus aktif
        $gwActive = Setting::get('payment_method_gateway', '0');
        $tmActive = Setting::get('payment_method_transfer_manual', '0');
        if ($gwActive !== '1' && $tmActive !== '1') {
            Setting::set('payment_method_gateway', '1', 'payment');
        }

        return redirect()->route('admin.integration.payment-gateway')
            ->with('success', 'Konfigurasi payment gateway berhasil disimpan!');
    }

    public function paymentGatewayTest(Request $request)
    {
        $gateway = $request->input('gateway');

        if ($gateway === 'xendit') {
            $result = (new XenditService())->testConnection();
        } elseif ($gateway === 'tripay') {
            $result = (new TripayService())->testConnection();
        } else {
            return back()->with('error', 'Gateway tidak dikenal.');
        }

        return $result['success']
            ? back()->with('success', ucfirst($gateway) . ' berhasil terhubung! ✓')
            : back()->with('error', ucfirst($gateway) . ' gagal: ' . ($result['error'] ?? 'Unknown error'));
    }
}
