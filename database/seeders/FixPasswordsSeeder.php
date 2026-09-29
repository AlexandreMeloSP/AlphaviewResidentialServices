<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FixPasswordsSeeder extends Seeder
{
    public function run(): void
    {
        if (env('APP_ENV') === 'production') {
            $this->command?->warn('FixPasswordsSeeder NÃO pode rodar em produção. Abortado.');
            return;
        }

        DB::table('users')->update(['password' => Hash::make('password')]);
    }
}
