<?php

namespace Database\Seeders;

use App\Models\Crypto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CryptoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Crypto::factory()
            ->count(10)
            ->create();
    }
}
