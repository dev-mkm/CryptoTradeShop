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
            $table->timestamps();
        });
        Schema::create('cryptoBalance', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('crypto_id')->constrained('cryptos')->cascadeOnDelete();
            $table->timestamp('date');
            $table->decimal('balance', 27, 18, true)->default(0);
            $table->primary(['user_id', 'crypto_id', 'date']);
        });
        Schema::create('crypto_prices', function (Blueprint $table) {
            $table->timestamp('time');
            $table->foreignId('crypto_id')->constrained('cryptos')->cascadeOnDelete();
            $table->decimal('price', 10, 2, true);
            $table->primary(['time', 'crypto_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crypto_prices');
        Schema::dropIfExists('cryptoBalance');
        Schema::dropIfExists('cryptos');
    }
};
