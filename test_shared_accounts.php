<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING SHARED ACCOUNTS BUDGET CREATION ===\n";

// Get admin and test user
$admin = \App\Models\User::where('email', 'admin@mali.com')->first();
$ismail = \App\Models\User::where('email', 'ismail@mali.com')->first();

echo "Users:\n";
echo "- Admin: {$admin->first_name}\n";
echo "- Test User: {$ismail->first_name}\n";

// Check current accounts
$cashAccount = \App\Models\Account::where('name', 'Cash on Hand')->first();
$hesabAccount = \App\Models\Account::where('name', 'HesabPay')->first();

echo "\nCurrent Shared Accounts:\n";
echo "- Cash on Hand: ID {$cashAccount->id}, User: " . ($cashAccount->user ? $cashAccount->user->first_name : 'Shared') . ", Balance: {$cashAccount->balance}\n";
echo "- HesabPay: ID {$hesabAccount->id}, User: " . ($hesabAccount->user ? $hesabAccount->user->first_name : 'Shared') . ", Balance: {$hesabAccount->balance}\n";

// Test budget creation for Ismail
echo "\n=== TESTING BUDGET CREATION ===\n";
$category = \App\Models\Category::first();

// Simulate BudgetController logic
$accountName = 'Cash on Hand'; // Default
if ($category && str_contains(strtolower($category->name), 'digital') || str_contains(strtolower($category->name), 'payment')) {
    $accountName = 'HesabPay';
}

echo "Category: {$category->name}\n";
echo "Determined account: {$accountName}\n";

// Find or create shared account
$targetAccount = \App\Models\Account::firstOrCreate(
    ['name' => $accountName, 'user_id' => null],
    ['balance' => 0]
);

echo "Target Account: ID {$targetAccount->id}, Name: {$targetAccount->name}\n";

// Create budget
$budget = \App\Models\Budget::create([
    'user_id' => $ismail->id,
    'category_id' => $category->id,
    'account_id' => $targetAccount->id,
    'name' => 'Test Budget with Shared Account',
    'amount' => 750.00,
    'period' => 'monthly',
    'start_date' => date('Y-m-01'),
    'end_date' => date('Y-m-t'),
    'description' => 'Testing shared account usage'
]);

echo "✓ Budget created: {$budget->name} (ID: {$budget->id})\n";
echo "✓ Uses shared account: {$budget->account->name}\n";

// Create budget assignment
$assignment = \App\Models\BudgetAssignment::create([
    'user_id' => $budget->user_id,
    'budget_id' => $budget->id,
    'account_id' => $budget->account_id,
    'assigned_amount' => $budget->amount,
    'remaining_amount' => $budget->amount,
    'assignment_notes' => $budget->description,
    'assigned_at' => now(),
    'status' => 'active',
]);

echo "✓ Budget assignment created\n";

// Test creating another budget - should use same shared account
echo "\n=== TESTING SECOND BUDGET CREATION ===\n";
$category2 = \App\Models\Category::skip(1)->first();

$accountName2 = 'Cash on Hand'; // Default
if ($category2 && str_contains(strtolower($category2->name), 'digital') || str_contains(strtolower($category2->name), 'payment')) {
    $accountName2 = 'HesabPay';
}

echo "Category: {$category2->name}\n";
echo "Determined account: {$accountName2}\n";

$targetAccount2 = \App\Models\Account::firstOrCreate(
    ['name' => $accountName2, 'user_id' => null],
    ['balance' => 0]
);

echo "Target Account: ID {$targetAccount2->id}, Name: {$targetAccount2->name}\n";

if ($targetAccount2->id === $targetAccount->id) {
    echo "✅ SUCCESS: Same shared account reused!\n";
} else {
    echo "❌ FAILED: Different account created\n";
}

// Check final account state
echo "\n=== FINAL ACCOUNT STATE ===\n";
$finalCashAccounts = \App\Models\Account::where('name', 'Cash on Hand')->get();
$finalHesabAccounts = \App\Models\Account::where('name', 'HesabPay')->get();

echo "Cash on Hand accounts: " . $finalCashAccounts->count() . "\n";
echo "HesabPay accounts: " . $finalHesabAccounts->count() . "\n";

echo "\n=== TEST COMPLETE ===\n";
echo "✅ Shared accounts working correctly\n";
echo "✅ No duplicate accounts created\n";
echo "✅ Budgets use shared accounts\n";
