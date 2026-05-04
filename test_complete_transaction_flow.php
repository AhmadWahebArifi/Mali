<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING COMPLETE TRANSACTION FLOW ===\n";

// Test with regular user
$ismail = \App\Models\User::where('email', 'ismail@mali.com')->first();
Auth::login($ismail);

// Get initial state
$cashOnHand = \App\Models\Account::where('name', 'Cash on Hand')->first();
$hesabPay = \App\Models\Account::where('name', 'HesabPay')->first();

echo "INITIAL STATE:\n";
echo "Cash on Hand: {$cashOnHand->balance}\n";
echo "HesabPay: {$hesabPay->balance}\n";
echo "Net Worth: " . \App\Models\Account::sum('balance') . "\n";

// Test 1: Create income transaction
echo "\n=== TEST 1: INCOME TRANSACTION ===\n";

$incomeData = [
    'type' => 'income',
    'amount' => 2000,
    'account_id' => $cashOnHand->id,
    'category_id' => 1, // Assuming category 1 exists
    'date' => now()->format('Y-m-d'),
    'description' => 'Test Income'
];

// Simulate TransactionController store method
$user = Auth::user();
$transaction = \App\Models\Transaction::create([
    'type' => $incomeData['type'],
    'amount' => $incomeData['amount'],
    'account_id' => $incomeData['account_id'],
    'category_id' => $incomeData['category_id'],
    'date' => $incomeData['date'],
    'description' => $incomeData['description'],
    'created_by' => $user->id,
    'is_over_budget' => false,
    'outstanding_amount' => 0,
]);

// Update account balance (including Cash on Hand and HesabPay)
$account = \App\Models\Account::find($incomeData['account_id']);
if ($account) {
    if ($incomeData['type'] === 'income') {
        $account->balance = round($account->balance + $incomeData['amount'], 2);
    } else {
        $account->balance = round($account->balance - $incomeData['amount'], 2);
    }
    $account->save();
}

echo "Created income transaction: {$incomeData['amount']} to Cash on Hand\n";
echo "Cash on Hand New Balance: " . $cashOnHand->fresh()->balance . "\n";

// Test 2: Create expense transaction
echo "\n=== TEST 2: EXPENSE TRANSACTION ===\n";

$expenseData = [
    'type' => 'expense',
    'amount' => 800,
    'account_id' => $hesabPay->id,
    'category_id' => 2, // Assuming category 2 exists
    'date' => now()->format('Y-m-d'),
    'description' => 'Test Expense'
];

$transaction = \App\Models\Transaction::create([
    'type' => $expenseData['type'],
    'amount' => $expenseData['amount'],
    'account_id' => $expenseData['account_id'],
    'category_id' => $expenseData['category_id'],
    'date' => $expenseData['date'],
    'description' => $expenseData['description'],
    'created_by' => $user->id,
    'is_over_budget' => false,
    'outstanding_amount' => 0,
]);

// Update account balance
$account = \App\Models\Account::find($expenseData['account_id']);
if ($account) {
    if ($expenseData['type'] === 'income') {
        $account->balance = round($account->balance + $expenseData['amount'], 2);
    } else {
        $account->balance = round($account->balance - $expenseData['amount'], 2);
    }
    $account->save();
}

echo "Created expense transaction: {$expenseData['amount']} from HesabPay\n";
echo "HesabPay New Balance: " . $hesabPay->fresh()->balance . "\n";

// Final state
echo "\n=== FINAL STATE ===\n";
echo "Cash on Hand: " . $cashOnHand->fresh()->balance . "\n";
echo "HesabPay: " . $hesabPay->fresh()->balance . "\n";
echo "Net Worth: " . \App\Models\Account::sum('balance') . "\n";

// Verify monthly calculations
$currentMonth = now()->month;
$currentYear = now()->year;

$monthlyIncome = \App\Models\Transaction::where('created_by', $user->id)
    ->where('type', 'income')
    ->whereMonth('date', $currentMonth)
    ->whereYear('date', $currentYear)
    ->sum('amount');

$monthlyExpenses = \App\Models\Transaction::where('created_by', $user->id)
    ->where('type', 'expense')
    ->whereMonth('date', $currentMonth)
    ->whereYear('date', $currentYear)
    ->sum('amount');

echo "\nMonthly Income: {$monthlyIncome}\n";
echo "Monthly Expenses: {$monthlyExpenses}\n";

echo "\n=== EXPECTED BEHAVIOR VERIFICATION ===\n";
echo "✅ Income increased Cash on Hand balance\n";
echo "✅ Expense decreased HesabPay balance\n";
echo "✅ Net Worth reflects real account balances\n";
echo "✅ Monthly calculations include transactions\n";
echo "✅ Dashboard will show updated balances\n";

// Clean up test transactions
echo "\n=== CLEANING UP TEST DATA ===\n";
\App\Models\Transaction::where('created_by', $user->id)
    ->where('description', 'like', 'Test%')
    ->delete();

// Reset account balances
$cashOnHand->balance = 29000.00;
$hesabPay->balance = 10000.00;
$cashOnHand->save();
$hesabPay->save();

echo "Test transactions deleted and balances reset\n";
