<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResetPasswordSeeder extends Seeder
{
    public function run(): void
    {
        if (env('APP_ENV') === 'production') {
            $this->command?->warn('ResetPasswordSeeder NÃO pode rodar em produção. Abortado.');
            return;
        }

        // Usar bcrypt diretamente, sem Hash::make() pois o cast 'hashed' já aplica
        DB::table('users')->where('id', 1)->update([
            'password' => bcrypt('password'),
        ]);
    }
}
