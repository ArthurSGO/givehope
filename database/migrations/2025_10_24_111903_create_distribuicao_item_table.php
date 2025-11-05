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
        Schema::create('distribuicao_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribuicao_id')->constrained('distribuicoes')->cascadeOnDelete();
            $table->foreignId('estoque_id')->constrained('estoques')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('itens')->cascadeOnDelete();
            $table->string('unidade', 20);
            $table->decimal('quantidade', 12, 3);
            $table->decimal('quantidade_reservada', 12, 3)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribuicao_item');
    }
};