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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('pay_token');
            $table->unsignedBigInteger('amount');
            $table->enum('state', [
                'waiting',
                'failed',
                'success'
            ])->default('waiting');
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate();
            $table->timestamp('date')->useCurrent();
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crypto_transactions');
        Schema::dropIfExists('transactions');
    }
};
