<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING DASHBOARD ACCOUNTS UPDATE ===\n";

// Test with regular user
$ismail = \App\Models\User::where('email', 'ismail@mali.com')->first();
Auth::login($ismail);

// Simulate the updated DashboardController logic
$user = Auth::user();
$isAdmin = $user->email === 'admin@mali.com';

// Get accounts for display (both admin and regular users can see Cash on Hand and HesabPay for transactions)
$accounts = \App\Models\Account::whereIn('name', ['Cash on Hand', 'HesabPay'])
    ->orderBy('name')
    ->get();

echo "User: {$user->first_name} (Regular User)\n";
echo "Accounts that will show on dashboard: {$accounts->count()}\n";

if ($accounts->count() > 0) {
    foreach ($accounts as $account) {
        echo "  - {$account->name} (ID: {$account->id}, Balance: {$account->balance})\n";
    }
} else {
    echo "❌ NO ACCOUNTS FOUND!\n";
}

// Test monthly income/expenses calculation
$currentMonth = now()->month;
$currentYear = now()->year;

$incomeQuery = \App\Models\Transaction::query();
if (!$isAdmin) {
    $incomeQuery->where('created_by', Auth::id());
}

$monthlyIncome = $incomeQuery->where('type', 'income')
    ->whereMonth('date', $currentMonth)
    ->whereYear('date', $currentYear)
    ->sum('amount');

$expenseQuery = \App\Models\Transaction::query();
if (!$isAdmin) {
    $expenseQuery->where('created_by', Auth::id());
}

$monthlyExpenses = $expenseQuery->where('type', 'expense')
    ->whereMonth('date', $currentMonth)
    ->whereYear('date', $currentYear)
    ->sum('amount');

echo "\nMonthly Income: ؋{$monthlyIncome}\n";
echo "Monthly Expenses: ؋{$monthlyExpenses}\n";

echo "\n=== TESTING ADMIN USER ===\n";

// Test with admin user
Auth::logout();
$admin = \App\Models\User::where('email', 'admin@mali.com')->first();
Auth::login($admin);

$user = Auth::user();
$isAdmin = $user->email === 'admin@mali.com';

$accounts = \App\Models\Account::whereIn('name', ['Cash on Hand', 'HesabPay'])
    ->orderBy('name')
    ->get();

echo "User: {$user->first_name} (Admin)\n";
echo "Accounts that will show on dashboard: {$accounts->count()}\n";
foreach ($accounts as $account) {
    echo "  - {$account->name} (ID: {$account->id}, Balance: {$account->balance})\n";
}

echo "\n=== EXPECTED DASHBOARD BEHAVIOR ===\n";
echo "✅ Both regular users and admin see Cash on Hand and HesabPay\n";
echo "✅ Monthly income/expenses calculated from user's transactions\n";
echo "✅ Account balances show available funds (not actual transaction amounts)\n";
