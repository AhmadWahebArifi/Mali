<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $this->call(AdminSeeder::class);
        
        // Create user-specific accounts
        $this->call(UserAccountsSeeder::class);
        
        // Create sample data
        $this->call(SampleDataSeeder::class);
        
        // User::factory(10)->create();
        User::factory()->create([
            'first_name' => 'Test User',
            'last_name' => 'Arifi',
            'email' => 'test@example.com',
        ]);
        $this->call(JobSeeder::class);
    }
}
