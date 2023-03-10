<?php

namespace Database\Factories;

use App\Models\Crypto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Offer>
 */
class OfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'crypto_id' => Crypto::factory(),
            'price' => fake()->randomNumber(),
            'amount' => fake()->randomNumber(),
            'selling' => fake()->boolean(),
        ];
    }
}
