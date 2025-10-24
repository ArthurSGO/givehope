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
        Schema::create('distribuicoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paroquia_id')->constrained()->cascadeOnDelete();
            $table->foreignId('beneficiario_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('reservado');
            $table->text('observacoes')->nullable();
            $table->timestamp('reservado_em')->nullable();
            $table->timestamp('enviado_em')->nullable();
            $table->timestamp('entregue_em')->nullable();
            $table->timestamp('estoque_debitado_em')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribuicoes');
    }
};