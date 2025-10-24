<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('estoque_movimentacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estoque_id')->constrained('estoques')->cascadeOnDelete();
            $table->foreignId('paroquia_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('distribuicao_id')->nullable()->constrained('distribuicoes')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tipo', 20);
            $table->decimal('quantidade', 12, 3);
            $table->string('unidade', 20);
            $table->string('motivo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estoque_movimentacoes');
    }
};