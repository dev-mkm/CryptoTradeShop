<?php

namespace Database\Seeders;

use App\Models\Crypto;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->hasAttached(Crypto::factory()
                ->count(3)
                ->create(), 
                ['balance' => fake()->randomNumber()])
            ->count(20)
            ->create();
    }
}
