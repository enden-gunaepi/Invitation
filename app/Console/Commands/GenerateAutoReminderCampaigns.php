<?php

namespace App\Console\Commands;

use App\Models\Invitation;
use App\Models\ReminderCampaign;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GenerateAutoReminderCampaigns extends Command
{
    protected $signature = 'reminders:generate-auto';
    protected $description = 'Generate automatic WhatsApp reminder campaigns for H-7/H-3/H-1 no_rsvp audience';

    public function handle(): int
    {
        $timeSetting = (string) Setting::get('reminder_auto_time', '09:00');
        $timeParts = explode(':', $timeSetting);
        $hour = (int) ($timeParts[0] ?? 9);
        $minute = (int) ($timeParts[1] ?? 0);

        $invitations = Invitation::query()
            ->with('user:id')
            ->where('status', 'active')
            ->whereDate('event_date', '>=', now()->toDateString())
            ->whereNotNull('event_date')
            ->get(['id', 'user_id', 'title', 'event_date', 'event_time', 'venue_name']);

        $slots = [7 => 'D7', 3 => 'D3', 1 => 'D1'];
        $created = 0;

        foreach ($invitations as $invitation) {
            foreach ($slots as $daysBefore => $slotKey) {
                $scheduledAt = Carbon::parse($invitation->event_date)
                    ->subDays($daysBefore)
                    ->setTime($hour, $minute, 0);

                if ($scheduledAt->isPast()) {
                    continue;
                }

                $scheduledKey = sprintf('%s-%s', $slotKey, $scheduledAt->toDateString());
                $payload = [
                    'created_by_user_id' => $invitation->user_id,
                    'channel' => 'whatsapp',
                    'audience' => 'no_rsvp',
                    'message_template' => $this->defaultTemplate($slotKey),
                    'scheduled_at' => $scheduledAt,
                    'status' => 'scheduled',
                    'source' => 'auto',
                    'notes' => "Auto reminder {$slotKey}",
                ];

                $campaign = ReminderCampaign::query()->firstOrNew([
                    'invitation_id' => $invitation->id,
                    'channel' => 'whatsapp',
                    'scheduled_key' => $scheduledKey,
                ]);

                if (!$campaign->exists) {
                    $campaign->fill($payload);
                    $campaign->scheduled_key = $scheduledKey;
                    $campaign->save();
                    $created++;
                }
            }
        }

        $this->info("Auto reminder generation done. Created={$created}");

        return self::SUCCESS;
    }

    private function defaultTemplate(string $slotKey): string
    {
        return match ($slotKey) {
            'D7' => 'Assalamu\'alaikum {name}, insyaAllah acara {event} akan dilaksanakan H-7 pada {date} {time} di {venue}. Mohon konfirmasi kehadiran melalui tautan ini: {link}',
            'D3' => 'Assalamu\'alaikum {name}, pengingat H-3 untuk acara {event} pada {date} {time} di {venue}. Kami menunggu konfirmasi kehadiran Anda: {link}',
            default => 'Assalamu\'alaikum {name}, pengingat H-1 acara {event} pada {date} {time} di {venue}. Sampai bertemu, konfirmasi di sini: {link}',
        };
    }
}
