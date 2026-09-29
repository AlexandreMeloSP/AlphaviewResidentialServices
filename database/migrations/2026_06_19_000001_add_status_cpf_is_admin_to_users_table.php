<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->after('email');
            $table->string('cpf', 11)->nullable()->unique()->after('status');
            $table->boolean('is_admin')->default(false)->after('cpf');
            $table->softDeletes()->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status', 'cpf', 'is_admin']);
            $table->dropSoftDeletes();
        });
    }
};
