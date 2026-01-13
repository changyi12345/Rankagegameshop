<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create or update admin user
        \App\Models\User::updateOrCreate(
            ['phone' => '09123456789'],
            [
                'name' => 'Admin',
                'email' => 'admin@rankage.com',
                'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
                'is_admin' => true,
            ]
        );

        // Create sample normal users for testing
        \App\Models\User::firstOrCreate(
            ['phone' => '09987654321'],
            [
                'name' => 'Test User 1',
                'email' => 'user1@test.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'balance' => 50000,
                'is_admin' => false,
            ]
        );

        \App\Models\User::firstOrCreate(
            ['phone' => '09876543210'],
            [
                'name' => 'Test User 2',
                'email' => 'user2@test.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'balance' => 25000,
                'is_admin' => false,
            ]
        );

        // Create sample games (if they don't exist)
        $mlbb = \App\Models\Game::firstOrCreate(
            ['name' => 'Mobile Legends'],
            [
                'icon' => '⚔️',
                'currency_name' => 'Diamonds',
                'requires_server' => false,
                'profit_margin' => 10,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $pubg = \App\Models\Game::firstOrCreate(
            ['name' => 'PUBG Mobile'],
            [
                'icon' => '🎯',
                'currency_name' => 'UC',
                'requires_server' => false,
                'profit_margin' => 12,
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $freefire = \App\Models\Game::firstOrCreate(
            ['name' => 'Free Fire'],
            [
                'icon' => '🔥',
                'currency_name' => 'Diamonds',
                'requires_server' => false,
                'profit_margin' => 10,
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        // Create sample packages for Mobile Legends (if they don't exist)
        \App\Models\Package::firstOrCreate(
            [
                'game_id' => $mlbb->id,
                'name' => '100 Diamonds',
            ],
            [
                'currency_amount' => 100,
                'price' => 1000,
                'bonus' => 0,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        \App\Models\Package::firstOrCreate(
            [
                'game_id' => $mlbb->id,
                'name' => '310 Diamonds',
            ],
            [
                'currency_amount' => 310,
                'price' => 3000,
                'bonus' => 10,
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        // Create sample packages for PUBG (if they don't exist)
        \App\Models\Package::firstOrCreate(
            [
                'game_id' => $pubg->id,
                'name' => '60 UC',
            ],
            [
                'currency_amount' => 60,
                'price' => 1200,
                'bonus' => 0,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        \App\Models\Package::firstOrCreate(
            [
                'game_id' => $pubg->id,
                'name' => '325 UC',
            ],
            [
                'currency_amount' => 325,
                'price' => 6000,
                'bonus' => 25,
                'is_active' => true,
                'sort_order' => 2,
            ]
        );
    }
}
