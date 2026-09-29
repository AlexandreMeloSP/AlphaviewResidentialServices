<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email_verification_token', 64)->nullable()->after('email');
            $table->boolean('mfa_enabled')->default(false)->after('is_admin');
            $table->string('mfa_secret', 64)->nullable()->after('mfa_enabled');
            $table->string('mfa_code', 6)->nullable()->after('mfa_secret');
            $table->timestamp('mfa_code_expires_at')->nullable()->after('mfa_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email_verification_token', 'mfa_enabled', 'mfa_secret', 'mfa_code', 'mfa_code_expires_at']);
        });
    }
};
