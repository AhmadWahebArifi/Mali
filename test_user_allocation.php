<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING USER-SPECIFIC ACCOUNT ALLOCATION ===\n";

// Get admin user and test user
$adminUser = \App\Models\User::where('email', 'admin@mali.com')->first();
$testUser = \App\Models\User::where('email', 'test@example.com')->first();

if (!$adminUser || !$testUser) {
    echo "Admin or test user not found!\n";
    exit;
}

echo "Admin User: {$adminUser->first_name} (ID: {$adminUser->id})\n";
echo "Test User: {$testUser->first_name} (ID: {$testUser->id})\n";

// Get test user's accounts
$cashAccount = \App\Models\Account::where('user_id', $testUser->id)
    ->where('name', 'Cash on Hand')
    ->first();

if (!$cashAccount) {
    echo "Test user's Cash on Hand account not found!\n";
    exit;
}

echo "\n1. Test user's Cash on Hand balance before allocation: " . $cashAccount->balance . "\n";

// Add funds to admin budget pool
echo "\n2. Adding 2000 to admin budget pool...\n";
$adminPool = \App\Models\AdminBudgetPool::getCurrent();
$adminPool->addFunds(2000);
echo "Admin pool available_funds: " . $adminPool->available_funds . "\n";

// Create budget for test user (this should transfer money to user's account)
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
$budget->name = 'Test Budget for User';
$budget->amount = 1000;
$budget->period = 'monthly';
$budget->start_date = date('Y-m-01');
$budget->end_date = date('Y-m-t');
$budget->save();

// Allocate from admin pool and transfer money to user's account
$adminPool->allocateBudget(1000, "Test allocation to user");
$cashAccount->balance = round($cashAccount->balance + 1000, 2);
$cashAccount->save();

echo "Budget created with amount: " . $budget->amount . "\n";
echo "Test user's Cash on Hand balance after allocation: " . $cashAccount->balance . "\n";
echo "Admin pool available_funds after allocation: " . $adminPool->fresh()->available_funds . "\n";

// Check total net worth across all users
echo "\n4. Total net worth across all users: ";
$totalNetWorth = \App\Models\Account::whereIn('name', ['Cash on Hand', 'HesabPay'])->sum('balance');
echo $totalNetWorth . "\n";

echo "\n=== Test completed successfully! ===\n";
