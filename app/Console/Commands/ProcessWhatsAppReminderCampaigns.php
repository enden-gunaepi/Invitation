<?php

namespace App\Console\Commands;

use App\Models\ReminderCampaign;
use App\Models\ReminderLog;
use App\Services\InvitationFunnelService;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;

class ProcessWhatsAppReminderCampaigns extends Command
{
    protected $signature = 'reminders:process-whatsapp';
    protected $description = 'Process scheduled WhatsApp reminder campaigns for invitation guests';

    public function handle(WhatsAppService $whatsAppService, InvitationFunnelService $funnelService): int
    {
        $campaigns = ReminderCampaign::with(['invitation', 'invitation.guests.rsvps'])
            ->where('channel', 'whatsapp')
            ->where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->limit(20)
            ->get();

        if ($campaigns->isEmpty()) {
            $this->info('No scheduled reminder campaign to process.');
            return self::SUCCESS;
        }

        foreach ($campaigns as $campaign) {
            $sent = 0;
            $failed = 0;

            $guests = $campaign->invitation->guests
                ->filter(fn ($g) => !empty($g->phone))
                ->values();

            if ($campaign->audience === 'not_checked_in') {
                $guests = $guests->filter(fn ($g) => is_null($g->checked_in_at))->values();
            } elseif ($campaign->audience === 'no_rsvp') {
                $guests = $guests->filter(fn ($g) => $g->rsvps->isEmpty())->values();
            }

            foreach ($guests as $guest) {
                $body = $this->renderTemplate($campaign->message_template, $campaign, $guest);
                $result = $whatsAppService->sendText((string) $guest->phone, $body);

                ReminderLog::create([
                    'campaign_id' => $campaign->id,
                    'guest_id' => $guest->id,
                    'phone' => $guest->phone,
                    'status' => $result['success'] ? 'sent' : 'failed',
                    'provider_message_id' => $result['message_id'] ?? null,
                    'response_message' => $result['success']
                        ? 'Terkirim'
                        : ($result['error'] ?? 'Gagal kirim'),
                ]);

                if ($result['success']) {
                    $sent++;
                    $funnelService->track((int) $campaign->invitation_id, 'sent', [
                        'guest_id' => $guest->id,
                        'guest_token' => $guest->token,
                        'phone' => $guest->phone,
                        'source' => $campaign->source ?? 'manual',
                        'meta' => [
                            'campaign_id' => $campaign->id,
                            'audience' => $campaign->audience,
                            'scheduled_key' => $campaign->scheduled_key,
                        ],
                    ]);
                } else {
                    $failed++;
                }
            }

            $campaign->update([
                'status' => $failed > 0 && $sent === 0 ? 'failed' : 'sent',
                'sent_count' => $sent,
                'failed_count' => $failed,
                'processed_at' => now(),
            ]);

            $this->info("Campaign #{$campaign->id} processed: sent={$sent}, failed={$failed}");
        }

        return self::SUCCESS;
    }

    private function renderTemplate(string $template, ReminderCampaign $campaign, $guest): string
    {
        $inv = $campaign->invitation;
        $personalLink = !empty($guest->token)
            ? url("/inv/{$inv->slug}/{$guest->token}")
            : $inv->getPublicUrl();

        $replace = [
            '{name}' => (string) $guest->name,
            '{event}' => (string) $inv->title,
            '{date}' => optional($inv->event_date)->format('d M Y') ?? '-',
            '{time}' => (string) $inv->event_time,
            '{venue}' => (string) $inv->venue_name,
            '{link}' => $personalLink,
        ];

        return str_replace(array_keys($replace), array_values($replace), $template);
    }
}
