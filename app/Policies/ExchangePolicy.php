<?php

namespace App\Policies;

use App\Models\Exchange;
use App\Models\User;

class ExchangePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isApproved();
    }

    public function view(User $user, Exchange $exchange): bool
    {
        return $user->id === $exchange->user_proponente_id
            || $user->id === $exchange->user_receptor_id;
    }

    public function create(User $user): bool
    {
        return $user->isApproved();
    }

    public function update(User $user, Exchange $exchange): bool
    {
        return $user->id === $exchange->user_proponente_id
            || $user->id === $exchange->user_receptor_id;
    }

    public function delete(User $user, Exchange $exchange): bool
    {
        return $user->id === $exchange->user_proponente_id
            || $user->id === $exchange->user_receptor_id;
    }
}
