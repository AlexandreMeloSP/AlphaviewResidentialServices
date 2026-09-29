<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_proponente_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('service_receptor_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('user_proponente_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_receptor_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchanges');
    }
};
