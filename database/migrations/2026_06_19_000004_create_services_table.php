<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('titulo', 150);
            $table->text('descricao');
            $table->string('categoria', 100);
            $table->decimal('valor_sugerido', 10, 2)->nullable();
            $table->string('imagem')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('categoria');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
