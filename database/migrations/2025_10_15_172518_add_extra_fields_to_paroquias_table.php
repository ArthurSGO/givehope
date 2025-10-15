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
        Schema::table('paroquias', function (Blueprint $table) {
            $table->string('nome_fantasia')->nullable()->after('nome');
            $table->string('abertura')->nullable()->after('cnpj');
            $table->string('porte')->nullable()->after('abertura');
            $table->string('natureza_juridica')->nullable()->after('porte');
            $table->string('situacao')->nullable()->after('natureza_juridica');
            $table->string('bairro')->nullable()->after('numero');
            $table->string('cep', 8)->nullable()->after('bairro');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paroquias', function (Blueprint $table) {
            $table->dropColumn(['nome_fantasia', 'abertura', 'porte', 'natureza_juridica', 'situacao', 'bairro', 'cep']);
        });
    }
};
