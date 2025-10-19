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
        Schema::create('doacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paroquia_id')->constrained('paroquias');
            $table->foreignId('doador_id')->nullable()->constrained('doadores');
            $table->enum('tipo', ['dinheiro', 'item']);
            $table->string('descricao')->nullable();
            $table->decimal('quantidade', 12, 2)->nullable();
            $table->enum('unidade', ['R$', 'Unidade', 'Kg']);
            $table->date('data_doacao');
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
        Schema::dropIfExists('doacoes');
    }
};
