<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\User;

class UserAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();
        
        foreach ($users as $user) {
            // Create Cash on Hand account for each user
            Account::firstOrCreate(
                ['user_id' => $user->id, 'name' => 'Cash on Hand'],
                ['balance' => 0]
            );
            
            // Create HesabPay account for each user
            Account::firstOrCreate(
                ['user_id' => $user->id, 'name' => 'HesabPay'],
                ['balance' => 0]
            );
        }

        $this->command->info('User-specific accounts seeded successfully.');
    }
}
