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
        Schema::create('paroquias', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('nome_fantasia')->nullable();
            $table->string('cnpj')->unique();
            $table->string('abertura')->nullable();
            $table->string('porte')->nullable();
            $table->string('natureza_juridica')->nullable();
            $table->string('situacao')->nullable();
            $table->string('logradouro');
            $table->string('numero')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cep', 8)->nullable();
            $table->string('cidade');
            $table->string('estado');
            $table->string('telefone');
            $table->string('email')->nullable();
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
        Schema::dropIfExists('paroquias');
    }
};