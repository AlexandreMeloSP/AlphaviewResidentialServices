<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CleanupDeletedUsers extends Command
{
    protected $signature = 'users:cleanup-deleted';
    protected $description = 'Exclui permanentemente usuários excluídos há mais de 7 dias';

    public function handle(): int
    {
        $cutoff = now()->subDays(7);

        $users = User::whereNotNull('deleted_at')
            ->where('deleted_at', '<=', $cutoff)
            ->get();

        $count = $users->count();

        foreach ($users as $user) {
            $user->cleanupRelatedData();
            $user->forceDelete();
        }

        $this->info("{$count} usuário(s) e dados relacionados excluído(s) permanentemente.");

        return 0;
    }
}
