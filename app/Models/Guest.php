<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class Guest extends Model
{
    protected $fillable = [
        'invitation_id', 'name', 'phone', 'email', 'token', 'category', 'pax', 'notes',
        'table_number', 'seat_label', 'checked_in_at', 'checkin_method', 'checked_in_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($guest) {
            if (empty($guest->token)) {
                $guest->token = Str::random(64);
            }
        });
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(Rsvp::class);
    }

    public function getInvitationUrl(): string
    {
        $path = "/wedding/{$this->invitation->slug}/{$this->getPublicToken()}";
        $request = Request::instance();

        if ($request) {
            return rtrim($request->getSchemeAndHttpHost(), '/') . $path;
        }

        return rtrim((string) Config::get('app.url'), '/') . $path;
    }

    public function getPublicToken(): string
    {
        $encodedId = strtoupper(base_convert((string) $this->id, 10, 36));
        $signature = strtoupper(substr(hash_hmac(
            'sha256',
            "{$this->invitation_id}|{$this->id}",
            (string) Config::get('app.key')
        ), 0, 6));

        return 'G' . $encodedId . $signature;
    }

    public function matchesPublicToken(string $token): bool
    {
        return hash_equals($this->getPublicToken(), strtoupper(trim($token)));
    }

    public static function resolveForInvitation(int $invitationId, string $token): ?self
    {
        $token = trim($token);
        if ($token === '') {
            return null;
        }

        $guest = self::query()
            ->where('invitation_id', $invitationId)
            ->where('token', $token)
            ->first();

        if ($guest) {
            return $guest;
        }

        $normalized = strtoupper($token);
        if (!preg_match('/^G([0-9A-Z]+)([0-9A-F]{6})$/', $normalized, $matches)) {
            return null;
        }

        $decodedId = base_convert($matches[1], 36, 10);
        if ($decodedId === '' || !ctype_digit((string) $decodedId)) {
            return null;
        }

        $guest = self::query()
            ->where('invitation_id', $invitationId)
            ->whereKey((int) $decodedId)
            ->first();

        if (!$guest || !$guest->matchesPublicToken($normalized)) {
            return null;
        }

        return $guest;
    }
}
