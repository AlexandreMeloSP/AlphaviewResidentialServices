<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminPassword = env('ADMIN_PASSWORD');

        if (! $adminPassword) {
            $this->command?->warn('ADMIN_PASSWORD não definido. AdminSeeder ignorado.');
            return;
        }

        $admin = User::where('email', 'admin@alphaview.com')->first();

        if (! $admin) {
            $admin = new User();
            $admin->forceFill([
                'email' => 'admin@alphaview.com',
                'name' => 'Administrador',
                'password' => $adminPassword,
                'status' => 'approved',
                'is_admin' => true,
                'email_verified_at' => now(),
            ])->save();
        } else {
            $admin->forceFill([
                'status' => 'approved',
                'is_admin' => true,
                'email_verified_at' => $admin->email_verified_at ?? now(),
            ])->save();
        }

        if (! Profile::where('user_id', $admin->id)->exists()) {
            Profile::forceCreate(['user_id' => $admin->id]);
        }

        $hasRole = \DB::table('user_roles')
            ->where('user_id', $admin->id)
            ->where('role_id', 1)
            ->exists();

        if (! $hasRole) {
            $admin->roles()->attach(1);
        }
    }
}
