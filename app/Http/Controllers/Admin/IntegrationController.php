<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\TelegramService;
use App\Services\WeaGateService;
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
            'telegram_bot_token' => Setting::get('telegram_bot_token', ''),
            'telegram_chat_id' => Setting::get('telegram_chat_id', ''),
            'telegram_enabled' => Setting::get('telegram_enabled', '0'),
            'telegram_webhook_active' => Setting::get('telegram_webhook_active', '0'),
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

        $keys = ['telegram_bot_token', 'telegram_chat_id', 'telegram_enabled'];

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
}
