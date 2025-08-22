<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('respostas_usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')
                ->constrained('usuarios')
                ->cascadeOnDelete();
            $table->foreignId('questao_id')
                ->constrained('questoes')
                ->cascadeOnDelete();
            $table->foreignId('alternativa_selecionada_id')
                ->nullable()
                ->constrained('alternativas')
                ->nullOnDelete();
            $table->timestamp('answered_at')->useCurrent();

            $table->unique(['usuario_id', 'questao_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('respostas_usuarios');
    }
};
