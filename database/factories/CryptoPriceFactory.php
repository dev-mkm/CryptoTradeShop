<?php

namespace Database\Factories;

use App\Models\Crypto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CryptoPrice>
 */
class CryptoPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'crypto_id' => Crypto::factory(),
            'price' => fake()->randomFloat(2),
            'time' => fake()->date()
        ];
    }
}
