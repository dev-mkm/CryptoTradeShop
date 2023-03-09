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
        Schema::create('cryptos', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name')->unique();
            $table->string('logo')->nullable();
            $table->decimal('price', 8, 2, true);
            $table->timestamps();
        });
        Schema::create('cryptoBalance', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('crypto_id')->constrained('cryptos')->cascadeOnDelete();
            $table->unsignedBigInteger('balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cryptoBalance');
        Schema::dropIfExists('cryptos');
    }
};
