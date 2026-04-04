<?php

namespace App\Services;

use App\Models\InvitationFunnelEvent;

class InvitationFunnelService
{
    public const EVENTS = ['sent', 'opened', 'map_clicked', 'rsvp_submitted', 'checked_in'];

    public function track(int $invitationId, string $event, array $context = []): void
    {
        if (!in_array($event, self::EVENTS, true)) {
            return;
        }

        InvitationFunnelEvent::create([
            'invitation_id' => $invitationId,
            'guest_id' => $context['guest_id'] ?? null,
            'event' => $event,
            'source' => $context['source'] ?? null,
            'guest_token' => $context['guest_token'] ?? null,
            'phone' => $context['phone'] ?? null,
            'ip_address' => $context['ip_address'] ?? null,
            'user_agent' => $context['user_agent'] ?? null,
            'meta' => $context['meta'] ?? null,
        ]);
    }

    public function summarize(int $invitationId): array
    {
        $events = InvitationFunnelEvent::query()
            ->where('invitation_id', $invitationId)
            ->selectRaw('event, COUNT(*) as total')
            ->groupBy('event')
            ->pluck('total', 'event');

        $sent = (int) ($events['sent'] ?? 0);
        $opened = (int) ($events['opened'] ?? 0);
        $mapClicked = (int) ($events['map_clicked'] ?? 0);
        $rsvp = (int) ($events['rsvp_submitted'] ?? 0);
        $checkedIn = (int) ($events['checked_in'] ?? 0);

        return [
            'sent' => $sent,
            'opened' => $opened,
            'map_clicked' => $mapClicked,
            'rsvp_submitted' => $rsvp,
            'checked_in' => $checkedIn,
            'conversion' => [
                'open_rate' => $this->rate($opened, $sent),
                'map_rate' => $this->rate($mapClicked, $opened),
                'rsvp_rate' => $this->rate($rsvp, $opened),
                'checkin_rate' => $this->rate($checkedIn, $rsvp),
            ],
        ];
    }

    private function rate(int $num, int $den): float
    {
        if ($den <= 0) {
            return 0.0;
        }

        return round(($num / $den) * 100, 2);
    }
}
