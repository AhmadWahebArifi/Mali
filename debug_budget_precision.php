<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUGGING BUDGET PRECISION ISSUES ===\n";

// Test AdminBudgetPool precision
echo "1. Testing AdminBudgetPool precision:\n";
$adminPool = \App\Models\AdminBudgetPool::getCurrent();
echo "Current total_budget: " . $adminPool->total_budget . "\n";
echo "Current total_allocated: " . $adminPool->total_allocated . "\n";
echo "Current available_funds: " . $adminPool->available_funds . "\n";

// Add 1000 to pool
echo "\n2. Adding 1000 to admin pool...\n";
$adminPool->total_budget = round($adminPool->total_budget + 1000, 2);
$adminPool->save();
echo "After adding 1000 - total_budget: " . $adminPool->fresh()->total_budget . "\n";
echo "After adding 1000 - available_funds: " . $adminPool->fresh()->available_funds . "\n";

// Allocate 1000 from pool
echo "\n3. Allocating 1000 from admin pool...\n";
$adminPool->total_allocated = round($adminPool->total_allocated + 1000, 2);
$adminPool->save();
echo "After allocating 1000 - total_allocated: " . $adminPool->fresh()->total_allocated . "\n";
echo "After allocating 1000 - available_funds: " . $adminPool->fresh()->available_funds . "\n";

// Test budget creation precision
echo "\n4. Testing budget creation precision:\n";
$user = \App\Models\User::where('email', 'test@example.com')->first();
$category = \App\Models\Category::first();
$account = \App\Models\Account::where('user_id', $user->id)->where('name', 'Cash on Hand')->first();

echo "Creating budget with amount 1000.00...\n";
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

echo "Budget created - amount: " . $budget->amount . "\n";
echo "Budget current_balance: " . $budget->current_balance . "\n";
echo "Budget remaining: " . $budget->remaining . "\n";

// Test account balance precision
echo "\n5. Testing account balance precision:\n";
echo "Account balance before: " . $account->balance . "\n";
$account->balance = round($account->balance + 1000, 2);
$account->save();
echo "Account balance after adding 1000: " . $account->fresh()->balance . "\n";

echo "\n=== DEBUG COMPLETE ===\n";
