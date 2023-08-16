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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate();
            $table->foreignId('crypto_id')->constrained('cryptos')->cascadeOnUpdate();
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('amount');
            $table->boolean('selling');
            $table->timestamps();
        });
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crypto_id')->constrained('cryptos')->cascadeOnUpdate();
            $table->unsignedBigInteger('price');
            $table->decimal('amount', 27, 18, true);
            $table->timestamps();
        });
        Schema::create('userTrades', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate();
            $table->foreignId('trade_id')->constrained('trades')->cascadeOnUpdate();
            $table->enum('role', ['Buyer', 'Seller']);
            $table->primary(['role', 'trade_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('userTrades');
        Schema::dropIfExists('trades');
        Schema::dropIfExists('offers');
    }
};
