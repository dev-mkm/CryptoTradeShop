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
        Schema::create('crypto_transactions', function (Blueprint $table) {
            $table->id();
            $table->boolean('in_out');
            $table->decimal('amount', 27, 18, true);
            $table->string('ct_token')->nullable();
            $table->enum('state', [
                'waiting',
                'failed',
                'success'
            ])->default('waiting');
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate();
            $table->foreignId('crypto_id')->constrained('cryptos')->cascadeOnUpdate();
            $table->timestamp('date')->useCurrent();
        });
        Schema::create('cryptoBalance', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate();
            $table->foreignId('crypto_id')->constrained('cryptos')->cascadeOnUpdate();
            $table->foreignId('c_t_id')->constrained('crypto_transactions')->cascadeOnUpdate();
            $table->decimal('balance', 27, 18, true)->default(0);
            $table->primary(['user_id', 'crypto_id', 'c_t_id']);
        });
        Schema::create('crypto_prices', function (Blueprint $table) {
            $table->timestamp('time')->useCurrent();
            $table->foreignId('crypto_id')->constrained('cryptos')->cascadeOnUpdate();
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
        Schema::dropIfExists('crypto_transactions');
        Schema::dropIfExists('cryptos');
    }
};
