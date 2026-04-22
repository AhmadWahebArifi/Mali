<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a user for the transactions
        $user = User::first() ?: User::factory()->create([
            'first_name' => 'Demo',
            'last_name' => 'User',
            'email' => 'demo@example.com',
            'password' => bcrypt('password')
        ]);

        // Create sample accounts
        $accounts = [
            ['name' => 'Cash on Hand', 'balance' => 2450.00],
            ['name' => 'HesabPay', 'balance' => 85240.00],
        ];

        foreach ($accounts as $accountData) {
            Account::create($accountData);
        }

        // Create sample categories
        $categories = [
            // Income categories
            ['name' => 'Salary', 'type' => 'income'],
            ['name' => 'Freelance', 'type' => 'income'],
            ['name' => 'Investments', 'type' => 'income'],
            
            // Expense categories
            ['name' => 'Food & Drink', 'type' => 'expense'],
            ['name' => 'Utilities', 'type' => 'expense'],
            ['name' => 'Transport', 'type' => 'expense'],
            ['name' => 'Electronics', 'type' => 'expense'],
            ['name' => 'Entertainment', 'type' => 'expense'],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        // Get the created accounts and categories
        $hesabPayAccount = Account::where('name', 'HesabPay')->first();
        $cashAccount = Account::where('name', 'Cash on Hand')->first();

        $salaryCategory = Category::where('name', 'Salary')->first();
        $foodCategory = Category::where('name', 'Food & Drink')->first();
        $utilitiesCategory = Category::where('name', 'Utilities')->first();
        $electronicsCategory = Category::where('name', 'Electronics')->first();
        $transportCategory = Category::where('name', 'Transport')->first();

        // Create sample transactions
        $transactions = [
            [
                'type' => 'expense',
                'amount' => 5.40,
                'description' => 'Starbucks Coffee',
                'date' => now(),
                'account_id' => $hesabPayAccount->id,
                'category_id' => $foodCategory->id,
                'created_by' => $user->id,
            ],
            [
                'type' => 'expense',
                'amount' => 142.00,
                'description' => 'Electric Bill',
                'date' => now()->subDay(),
                'account_id' => $hesabPayAccount->id,
                'category_id' => $utilitiesCategory->id,
                'created_by' => $user->id,
            ],
            [
                'type' => 'income',
                'amount' => 6200.00,
                'description' => 'TechCorp Salary',
                'date' => now()->subDays(5),
                'account_id' => $hesabPayAccount->id,
                'category_id' => $salaryCategory->id,
                'created_by' => $user->id,
            ],
            [
                'type' => 'expense',
                'amount' => 999.00,
                'description' => 'Apple Store',
                'date' => now()->subDays(6),
                'account_id' => $hesabPayAccount->id,
                'category_id' => $electronicsCategory->id,
                'created_by' => $user->id,
            ],
            [
                'type' => 'expense',
                'amount' => 24.50,
                'description' => 'Uber Ride',
                'date' => now()->subDays(7),
                'account_id' => $cashAccount->id,
                'category_id' => $transportCategory->id,
                'created_by' => $user->id,
            ],
        ];

        foreach ($transactions as $transactionData) {
            Transaction::create($transactionData);
        }

        // Create some historical transactions for the chart
        for ($i = 1; $i <= 5; $i++) {
            $month = now()->subMonths($i);
            
            // Income for each month
            Transaction::create([
                'type' => 'income',
                'amount' => 6000 + rand(200, 800),
                'description' => 'Monthly Salary',
                'date' => $month->copy()->startOfMonth()->addDays(15),
                'account_id' => $hesabPayAccount->id,
                'category_id' => $salaryCategory->id,
                'created_by' => $user->id,
            ]);
            
            // Expenses for each month
            for ($j = 0; $j < 3; $j++) {
                Transaction::create([
                    'type' => 'expense',
                    'amount' => rand(500, 2000),
                    'description' => 'Monthly Expense ' . ($j + 1),
                    'date' => $month->copy()->addDays($j * 10),
                    'account_id' => $hesabPayAccount->id,
                    'category_id' => $foodCategory->id,
                    'created_by' => $user->id,
                ]);
            }
        }
    }
}
