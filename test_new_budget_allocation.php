<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING NEW BUDGET ALLOCATION PRECISION ===\n";

// Get admin pool current state
$adminPool = \App\Models\AdminBudgetPool::getCurrent();
echo "Current admin pool state:\n";
echo "- total_budget: " . $adminPool->total_budget . "\n";
echo "- total_allocated: " . $adminPool->total_allocated . "\n";
echo "- available_funds: " . $adminPool->available_funds . "\n";

// Add funds to pool
echo "\n1. Adding 1000 to admin pool...\n";
$adminPool->addFunds(1000);
echo "After adding funds:\n";
echo "- total_budget: " . $adminPool->fresh()->total_budget . "\n";
echo "- available_funds: " . $adminPool->fresh()->available_funds . "\n";

// Create a new budget allocation
echo "\n2. Creating new budget allocation of 1000...\n";
$user = \App\Models\User::where('email', 'ismail@mali.com')->first();
$category = \App\Models\Category::first();
$account = \App\Models\Account::where('user_id', $user->id)->where('name', 'Cash on Hand')->first();

$budget = \App\Models\Budget::create([
    'user_id' => $user->id,
    'category_id' => $category->id,
    'account_id' => $account->id,
    'name' => 'Precision Test Budget',
    'amount' => 1000.00,
    'period' => 'monthly',
    'start_date' => date('Y-m-01'),
    'end_date' => date('Y-m-t'),
]);

// Allocate from admin pool
$adminPool->allocateBudget(1000);

// Transfer to user account
$account->balance = round($account->balance + 1000, 2);
$account->save();

echo "Budget created:\n";
echo "- Budget amount: " . $budget->amount . "\n";
echo "- Budget remaining: " . $budget->remaining . "\n";
echo "- User account balance: " . $account->fresh()->balance . "\n";
echo "- Admin pool total_allocated: " . $adminPool->fresh()->total_allocated . "\n";
echo "- Admin pool available_funds: " . $adminPool->fresh()->available_funds . "\n";

echo "\n=== PRECISION TEST COMPLETE ===\n";
