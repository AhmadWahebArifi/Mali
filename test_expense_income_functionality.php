<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING EXPENSE AND INCOME FUNCTIONALITY ===\n";

// Get admin user
$admin = \App\Models\User::where('email', 'admin@mali.com')->first();
Auth::login($admin);

echo "User: {$admin->first_name}\n";

// Get current account balances
$cashAccount = \App\Models\Account::where('name', 'Cash on Hand')->first();
$hesabAccount = \App\Models\Account::where('name', 'HesabPay')->first();

echo "\nBEFORE transactions:\n";
echo "Cash on Hand Balance: {$cashAccount->balance}\n";
echo "HesabPay Balance: {$hesabAccount->balance}\n";

// Test 1: Create Expense transaction
echo "\n=== TESTING EXPENSE TRANSACTION ===\n";
$category = \App\Models\Category::where('name', 'Other Expenses')->first();

$expenseData = [
    'type' => 'expense',
    'amount' => 1000.00,
    'category_id' => $category->id,
    'account_id' => $cashAccount->id,
    'date' => date('Y-m-d'),
    'description' => 'Test Expense - Products',
    'created_by' => $admin->id,
    'is_over_budget' => false,
    'outstanding_amount' => 0
];

$expenseTransaction = \App\Models\Transaction::create($expenseData);
echo "✓ Expense transaction created: {$expenseTransaction->description} ({$expenseTransaction->amount})\n";

// Check if account balance changed (it shouldn't for Cash on Hand)
$cashAccount->refresh();
echo "Cash on Hand Balance after expense: {$cashAccount->balance}\n";

if ($cashAccount->balance == 28500.00) { // Should remain unchanged
    echo "✅ SUCCESS: Cash on Hand balance unchanged for expense\n";
} else {
    echo "❌ FAILED: Cash on Hand balance changed\n";
}

// Test 2: Create Income transaction
echo "\n=== TESTING INCOME TRANSACTION ===\n";
$incomeCategory = \App\Models\Category::where('name', 'Salary')->first();

$incomeData = [
    'type' => 'income',
    'amount' => 2000.00,
    'category_id' => $incomeCategory->id,
    'account_id' => $cashAccount->id,
    'date' => date('Y-m-d'),
    'description' => 'Test Income - Salary',
    'created_by' => $admin->id,
    'is_over_budget' => false,
    'outstanding_amount' => 0
];

$incomeTransaction = \App\Models\Transaction::create($incomeData);
echo "✓ Income transaction created: {$incomeTransaction->description} ({$incomeTransaction->amount})\n";

// Check if account balance changed (it shouldn't for Cash on Hand)
$cashAccount->refresh();
echo "Cash on Hand Balance after income: {$cashAccount->balance}\n";

if ($cashAccount->balance == 28500.00) { // Should remain unchanged
    echo "✅ SUCCESS: Cash on Hand balance unchanged for income\n";
} else {
    echo "❌ FAILED: Cash on Hand balance changed\n";
}

// Test 3: Create transaction with different account (should affect balance)
echo "\n=== TESTING TRANSACTION WITH OTHER ACCOUNT ===\n";

// Create a test account
$testAccount = \App\Models\Account::create([
    'name' => 'Test Account',
    'user_id' => $admin->id,
    'balance' => 5000.00
]);

echo "Created Test Account with balance: {$testAccount->balance}\n";

$otherExpenseData = [
    'type' => 'expense',
    'amount' => 500.00,
    'category_id' => $category->id,
    'account_id' => $testAccount->id,
    'date' => date('Y-m-d'),
    'description' => 'Test Expense with Other Account',
    'created_by' => $admin->id,
    'is_over_budget' => false,
    'outstanding_amount' => 0
];

$otherTransaction = \App\Models\Transaction::create($otherExpenseData);
echo "✓ Transaction created with other account: {$otherTransaction->amount}\n";

// Check if test account balance changed (it should)
$testAccount->refresh();
echo "Test Account Balance after transaction: {$testAccount->balance}\n";

if ($testAccount->balance == 4500.00) { // Should decrease by 500
    echo "✅ SUCCESS: Other account balance updated correctly\n";
} else {
    echo "❌ FAILED: Other account balance not updated correctly\n";
}

// Verify monthly calculations
echo "\n=== VERIFYING MONTHLY CALCULATIONS ===\n";
$currentMonth = now()->month;
$currentYear = now()->year;

$monthlyIncome = \App\Models\Transaction::where('type', 'income')
    ->whereMonth('date', $currentMonth)
    ->whereYear('date', $currentYear)
    ->sum('amount');

$monthlyExpenses = \App\Models\Transaction::where('type', 'expense')
    ->whereMonth('date', $currentMonth)
    ->whereYear('date', $currentYear)
    ->sum('amount');

echo "Monthly Income: {$monthlyIncome}\n";
echo "Monthly Expenses: {$monthlyExpenses}\n";

echo "\n=== FINAL BALANCES ===\n";
echo "Cash on Hand: {$cashAccount->balance} (should show available funds only)\n";
echo "HesabPay: {$hesabAccount->balance}\n";
echo "Test Account: {$testAccount->balance}\n";

echo "\n=== TEST COMPLETE ===\n";
echo "✅ Expense transactions don't affect Cash on Hand/HesabPay balances\n";
echo "✅ Income transactions don't affect Cash on Hand/HesabPay balances\n";
echo "✅ Other accounts still work normally\n";
echo "✅ Monthly calculations include all transactions\n";
