<?php

namespace Database\Seeders;

use App\Models\Crypto;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Trade::factory()
            ->count(10)
            ->hasAttached(User::factory()
                ->count(1)
                ->create(), 
                ['role' => 'Buyer'])
            ->hasAttached(User::factory()
                ->count(1)
                ->create(), 
                ['role' => 'Seller'])
            ->create();
    }
}
