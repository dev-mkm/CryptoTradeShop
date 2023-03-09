<?php

namespace Database\Factories;

use App\Models\Crypto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trade>
 */
class TradeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'crypto' => Crypto::factory(),
            'price' => fake()->randomNumber(),
            'amount' => fake()->randomNumber(),
            'cryptovalue' => function (array $attributes) {
                return Crypto::find($attributes['crypto'])->price;
            },
        ];
    }
}
