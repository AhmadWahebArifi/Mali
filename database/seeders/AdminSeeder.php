<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@mali.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
            'is_approved' => true,
            'approved_at' => now(),
        ]);
    }
}
