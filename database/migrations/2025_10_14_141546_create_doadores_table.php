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
        Schema::create('doadores', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cpf_cnpj', 14)->nullable();
            $table->string('telefone', 11)->nullable();
            $table->string('logradouro')->nullable();
            $table->string('cep', 8)->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('cidade', 100)->nullable();
            $table->string('estado', 100)->nullable();
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
        Schema::dropIfExists('doadores');
    }
};