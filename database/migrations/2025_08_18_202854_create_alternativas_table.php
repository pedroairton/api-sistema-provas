<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('alternativas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('questao_id')
                ->constrained('questoes')
                ->cascadeOnDelete();
            $table->text('texto');
            $table->boolean('correta')->default(false);
        });
    }

    public function down(): void {
        Schema::dropIfExists('alternativas');
    }
};