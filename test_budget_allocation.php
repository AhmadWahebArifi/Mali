<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING BUDGET ALLOCATION ===\n";

// Get admin user and test user
$adminUser = \App\Models\User::where('email', 'admin@mali.com')->first();
$testUser = \App\Models\User::where('email', 'test@example.com')->first();

if (!$adminUser || !$testUser) {
    echo "Admin or test user not found!\n";
    exit;
}

// Get global accounts
$cashAccount = \App\Models\Account::where('name', 'Cash on Hand')->whereNull('user_id')->first();
if (!$cashAccount) {
    echo "Global Cash on Hand account not found!\n";
    exit;
}

// Add funds to global account first (simulating admin adding money)
echo "1. Adding 2000 to global Cash on Hand account...\n";
$cashAccount->balance = round($cashAccount->balance + 2000, 2);
$cashAccount->save();
echo "Global account balance: " . $cashAccount->balance . "\n";

// Add funds to admin budget pool
echo "\n2. Adding 2000 to admin budget pool...\n";
$adminPool = \App\Models\AdminBudgetPool::getCurrent();
$adminPool->addFunds(2000);
echo "Admin pool total_budget: " . $adminPool->total_budget . "\n";
echo "Admin pool available_funds: " . $adminPool->available_funds . "\n";

// Create budget for test user (this should transfer money)
echo "\n3. Creating 1000 budget for test user...\n";
$category = \App\Models\Category::first();
if (!$category) {
    echo "No category found!\n";
    exit;
}

$budget = new \App\Models\Budget();
$budget->user_id = $testUser->id;
$budget->category_id = $category->id;
$budget->account_id = $cashAccount->id;
$budget->name = 'Test Budget';
$budget->amount = 1000;
$budget->period = 'monthly';
$budget->start_date = date('Y-m-01');
$budget->end_date = date('Y-m-t');
$budget->save();

// Allocate from admin pool (this should transfer money to user's account)
$adminPool->allocateBudget(1000, "Test allocation");

// Transfer actual money to user's account
$cashAccount->balance = round($cashAccount->balance + 1000, 2);
$cashAccount->save();

echo "Budget created with amount: " . $budget->amount . "\n";
echo "Global account balance after budget creation: " . $cashAccount->balance . "\n";
echo "Admin pool available_funds after allocation: " . $adminPool->fresh()->available_funds . "\n";

// Test budget deletion (should return money to admin pool and deduct from user account)
echo "\n4. Deleting budget (should return money)...\n";
$budgetAmount = $budget->amount;
$budget->delete();

// Return funds to admin pool
$adminPool->returnBudget($budgetAmount);

// Deduct money from user's account
$cashAccount->balance = round($cashAccount->balance - $budgetAmount, 2);
$cashAccount->save();

echo "Global account balance after budget deletion: " . $cashAccount->balance . "\n";
echo "Admin pool available_funds after returning: " . $adminPool->fresh()->available_funds . "\n";

echo "\n=== Test completed ===\n";
