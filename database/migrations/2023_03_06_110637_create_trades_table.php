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
            $table->foreignId('user')->constrained('users')->cascadeOnDelete();
            $table->foreignId('crypto')->constrained('cryptos')->cascadeOnDelete();
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('amount');
            $table->boolean('selling');
            $table->timestamps();
        });
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crypto')->constrained('cryptos')->cascadeOnDelete();
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('amount');
            $table->decimal('cryptovalue', 8, 2, true);
            $table->timestamps();
        });
        Schema::create('userTrades', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('trade_id')->constrained('trades')->cascadeOnDelete();
            $table->enum('role', ['Buyer', 'Seller']);
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
