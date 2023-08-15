<?php

namespace Database\Seeders;

use App\Models\Crypto;
use App\Models\CryptoPrice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CryptoPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CryptoPrice::factory()
            ->count(10)
            ->create();
    }
}
