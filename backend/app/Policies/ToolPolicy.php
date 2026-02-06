<?php

namespace App\Policies;

use App\Models\Tool;
use App\Models\User;

class ToolPolicy
{
    /**
     * Allow update if user is the tool owner, or has Owner/Admin role (explicit permission).
     */
    public function update(User $user, Tool $tool): bool
    {
        return $tool->user_id === $user->id
            || $user->isOwner()
            || $user->isAdmin();
    }

    /**
     * Allow delete if user is the tool owner, or has Owner/Admin role (explicit permission).
     */
    public function delete(User $user, Tool $tool): bool
    {
        return $tool->user_id === $user->id
            || $user->isOwner()
            || $user->isAdmin();
    }

    /**
     * Allow approve/reject only for Owner or Admin role (explicit permission).
     */
    public function approve(User $user, Tool $tool): bool
    {
        return $user->isOwner() || $user->isAdmin();
    }
}
