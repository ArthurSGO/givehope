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
        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->string('numero', 10)->nullable()->after('endereco');
            $table->string('complemento', 100)->nullable()->after('numero');
            $table->string('bairro', 100)->nullable()->after('complemento');
            $table->string('cidade', 100)->nullable()->after('bairro');
            $table->string('estado', 2)->nullable()->after('cidade');
            $table->string('cep', 8)->nullable()->after('estado');
            $table->string('cpf', 11)->nullable()->after('cep');
            $table->string('rg', 20)->nullable()->after('cpf');
            $table->date('data_nascimento')->nullable()->after('rg');
            $table->string('email', 150)->nullable()->after('data_nascimento');
            $table->string('ponto_referencia', 150)->nullable()->after('email');
            $table->text('observacoes')->nullable()->after('ponto_referencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->dropColumn([
                'numero',
                'complemento',
                'bairro',
                'cidade',
                'estado',
                'cep',
                'cpf',
                'rg',
                'data_nascimento',
                'email',
                'ponto_referencia',
                'observacoes',
            ]);
        });
    }
};