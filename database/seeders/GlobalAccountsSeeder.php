<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class GlobalAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update global Cash on Hand account
        Account::updateOrCreate(
            ['name' => 'Cash on Hand'],
            [
                'balance' => 0,
                'user_id' => null, // Global account - no specific user
            ]
        );

        // Create or update global HesabPay account
        Account::updateOrCreate(
            ['name' => 'HesabPay'],
            [
                'balance' => 0,
                'user_id' => null, // Global account - no specific user
            ]
        );

        $this->command->info('Global accounts seeded successfully.');
    }
}
