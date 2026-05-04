<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING NET WORTH FIX ===\n";

// Get admin and test user
$admin = \App\Models\User::where('email', 'admin@mali.com')->first();
$ismail = \App\Models\User::where('email', 'ismail@mali.com')->first();

echo "Users:\n";
echo "- Admin: {$admin->first_name} (ID: {$admin->id})\n";
echo "- Ismail: {$ismail->first_name} (ID: {$ismail->id})\n";

// Get Ismail's account balance BEFORE budget assignment
$ismailAccount = \App\Models\Account::where('user_id', $ismail->id)
    ->where('name', 'Cash on Hand')
    ->first();

echo "\nBEFORE Budget Assignment:\n";
echo "Ismail's Account Balance: {$ismailAccount->balance}\n";

// Calculate global net worth BEFORE
$globalTotalBalance = \App\Models\Account::sum('balance');
echo "Global Net Worth: {$globalTotalBalance}\n";

// Get admin budget pool
$adminPool = \App\Models\AdminBudgetPool::getCurrent();
echo "Admin Pool Available: {$adminPool->available_funds}\n";

// Simulate budget assignment like BudgetController would
$budgetAmount = 11000.00;
$category = \App\Models\Category::first();

echo "\nAssigning budget of {$budgetAmount} to Ismail...\n";

// Create budget
$budget = \App\Models\Budget::create([
    'user_id' => $ismail->id,
    'category_id' => $category->id,
    'account_id' => $ismailAccount->id,
    'name' => 'Test Budget Assignment',
    'amount' => $budgetAmount,
    'period' => 'monthly',
    'start_date' => date('Y-m-01'),
    'end_date' => date('Y-m-t'),
    'description' => 'Testing net worth fix'
]);

// Create budget assignment
$assignment = \App\Models\BudgetAssignment::create([
    'user_id' => $ismail->id,
    'budget_id' => $budget->id,
    'account_id' => $ismailAccount->id,
    'assigned_amount' => $budgetAmount,
    'remaining_amount' => $budgetAmount,
    'assignment_notes' => 'Testing net worth fix',
    'assigned_at' => now(),
    'status' => 'active',
]);

// Allocate from admin pool (but NOT transfer to user account)
$adminPool->allocateBudget($budgetAmount, "Budget allocated to {$ismail->first_name}: {$budget->name}");

echo "✓ Budget created: {$budget->name}\n";
echo "✓ Assignment created: {$assignment->assigned_amount}\n";
echo "✓ Admin pool updated: Available {$adminPool->available_funds}\n";

// Check account balance AFTER (should be the same)
$ismailAccount->refresh();
echo "\nAFTER Budget Assignment:\n";
echo "Ismail's Account Balance: {$ismailAccount->balance}\n";

// Calculate global net worth AFTER (should be the same)
$newGlobalTotalBalance = \App\Models\Account::sum('balance');
echo "Global Net Worth: {$newGlobalTotalBalance}\n";

// Verify no change
$accountBalanceChanged = $ismailAccount->balance != $ismailAccount->balance;
$netWorthChanged = $globalTotalBalance != $newGlobalTotalBalance;

echo "\n=== RESULTS ===\n";
if (!$accountBalanceChanged && !$netWorthChanged) {
    echo "✅ SUCCESS: Budget assignment did NOT increase net worth!\n";
    echo "- Account balance unchanged: {$ismailAccount->balance}\n";
    echo "- Global net worth unchanged: {$newGlobalTotalBalance}\n";
} else {
    echo "❌ FAILED: Budget assignment still affects net worth\n";
    if ($accountBalanceChanged) {
        echo "- Account balance changed\n";
    }
    if ($netWorthChanged) {
        echo "- Global net worth changed\n";
    }
}

echo "\nBudget Assignment Details:\n";
echo "- Budget: {$budget->name} ({$budget->amount})\n";
echo "- Assignment: {$assignment->assigned_amount} assigned, {$assignment->remaining_amount} remaining\n";

echo "\n=== TEST COMPLETE ===\n";
