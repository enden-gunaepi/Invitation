<?php

namespace App\Services;

use App\Models\Invitation;

class InvitationAccessService
{
    public function isOwnerOrEditor(Invitation $invitation, int $userId): bool
    {
        if ((int) $invitation->user_id === $userId) {
            return true;
        }

        return $invitation->collaborators()
            ->where('user_id', $userId)
            ->where('status', 'accepted')
            ->where('role', 'editor')
            ->exists();
    }

    public function isOwner(Invitation $invitation, int $userId): bool
    {
        return (int) $invitation->user_id === $userId;
    }
}
