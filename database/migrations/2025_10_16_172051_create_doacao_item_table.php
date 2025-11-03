<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doacao_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doacao_id')->constrained('doacoes')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('itens')->onDelete('cascade');
            $table->decimal('quantidade', 10, 2);
            $table->enum('unidade', ['Unidade', 'Kg']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doacao_item');
    }
};
