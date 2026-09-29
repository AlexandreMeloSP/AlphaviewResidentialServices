<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Service $service): bool
    {
        return $service->status === 'active' || $user->id === $service->user_id || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        if (! $user->isApproved()) {
            return false;
        }

        $servicosAtivos = $user->services()->count();

        return $servicosAtivos < 5;
    }

    public function update(User $user, Service $service): bool
    {
        return $user->id === $service->user_id;
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->id === $service->user_id;
    }
}
