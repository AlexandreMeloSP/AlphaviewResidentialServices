<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;

class ContractPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isApproved();
    }

    public function view(User $user, Contract $contract): bool
    {
        return $user->id === $contract->user_1_id
            || $user->id === $contract->user_2_id
            || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isApproved();
    }

    public function update(User $user, Contract $contract): bool
    {
        return $user->id === $contract->user_1_id
            || $user->id === $contract->user_2_id;
    }

    public function sign(User $user, Contract $contract): bool
    {
        return ($user->id === $contract->user_1_id || $user->id === $contract->user_2_id)
            && $user->isApproved();
    }
}
