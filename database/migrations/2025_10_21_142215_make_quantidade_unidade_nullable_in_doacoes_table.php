<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doacoes', function (Blueprint $table) {
            $table->decimal('quantidade', 12, 2)->nullable()->change();
            $table->string('unidade', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('doacoes', function (Blueprint $table) {
             $table->decimal('quantidade', 12, 2)->nullable(false)->change();
             $table->string('unidade', 50)->nullable(false)->change();
        });
    }
};