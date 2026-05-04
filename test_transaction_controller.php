<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING TRANSACTION CONTROLLER LOGIC ===\n";

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

// Simulate TransactionController store method logic
echo "\n=== SIMULATING TRANSACTION CONTROLLER STORE ===\n";

// Test 1: Expense transaction
$category = \App\Models\Category::where('name', 'Other Expenses')->first();

$validated = [
    'type' => 'expense',
    'amount' => 1000.00,
    'category_id' => $category->id,
    'account_id' => $cashAccount->id,
    'date' => date('Y-m-d'),
    'description' => 'Test Expense - Products'
];

// Simulate the TransactionController store method
$user = Auth::user();

// Budget enforcement
$budgetId = null;
$isOverBudget = false;
$outstandingAmount = 0;

if ($validated['type'] === 'expense') {
    // Find user budget (simplified)
    $budget = null; // Skip budget finding for this test
    $budgetId = $budget ? $budget->id : null;
}

// Create transaction
$transaction = \App\Models\Transaction::create([
    'type' => $validated['type'],
    'amount' => $validated['amount'],
    'category_id' => $validated['category_id'],
    'account_id' => $validated['account_id'],
    'budget_id' => $budgetId,
    'date' => $validated['date'],
    'description' => $validated['description'] ?? '',
    'created_by' => $user->id,
    'is_over_budget' => $isOverBudget,
    'outstanding_amount' => $outstandingAmount,
]);

echo "✓ Expense transaction created: {$transaction->description} ({$transaction->amount})\n";

// Apply the NEW logic from TransactionController
$account = \App\Models\Account::find($validated['account_id']);
if ($account) {
    // Don't update Cash on Hand and HesabPay balances - they represent available funds, not actual cash
    if (!in_array($account->name, ['Cash on Hand', 'HesabPay'])) {
        if ($validated['type'] === 'income') {
            $account->balance = round($account->balance + $validated['amount'], 2);
        } else {
            $account->balance = round($account->balance - $validated['amount'], 2);
        }
        $account->save();
        echo "✓ Account balance updated\n";
    } else {
        echo "✓ Skipped balance update for Cash on Hand/HesabPay\n";
    }
}

// Check result
$cashAccount->refresh();
echo "Cash on Hand Balance after expense: {$cashAccount->balance}\n";

// Test 2: Income transaction
echo "\n=== TESTING INCOME TRANSACTION ===\n";
$incomeCategory = \App\Models\Category::where('name', 'Salary')->first();

$validatedIncome = [
    'type' => 'income',
    'amount' => 2000.00,
    'category_id' => $incomeCategory->id,
    'account_id' => $cashAccount->id,
    'date' => date('Y-m-d'),
    'description' => 'Test Income - Salary'
];

$incomeTransaction = \App\Models\Transaction::create([
    'type' => $validatedIncome['type'],
    'amount' => $validatedIncome['amount'],
    'category_id' => $validatedIncome['category_id'],
    'account_id' => $validatedIncome['account_id'],
    'budget_id' => null,
    'date' => $validatedIncome['date'],
    'description' => $validatedIncome['description'],
    'created_by' => $user->id,
    'is_over_budget' => false,
    'outstanding_amount' => 0,
]);

echo "✓ Income transaction created: {$incomeTransaction->description} ({$incomeTransaction->amount})\n";

// Apply the NEW logic
$account = \App\Models\Account::find($validatedIncome['account_id']);
if ($account) {
    if (!in_array($account->name, ['Cash on Hand', 'HesabPay'])) {
        if ($validatedIncome['type'] === 'income') {
            $account->balance = round($account->balance + $validatedIncome['amount'], 2);
        } else {
            $account->balance = round($account->balance - $validatedIncome['amount'], 2);
        }
        $account->save();
        echo "✓ Account balance updated\n";
    } else {
        echo "✓ Skipped balance update for Cash on Hand/HesabPay\n";
    }
}

// Check final result
$cashAccount->refresh();
echo "Cash on Hand Balance after income: {$cashAccount->balance}\n";

echo "\n=== RESULTS ===\n";
if ($cashAccount->balance == 29000.00) {
    echo "✅ SUCCESS: Cash on Hand balance unchanged for both expense and income\n";
} else {
    echo "❌ FAILED: Cash on Hand balance changed\n";
}

echo "\n=== TEST COMPLETE ===\n";
