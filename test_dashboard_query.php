<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING DASHBOARD QUERY LOGIC ===\n";

// Simulate admin user
$admin = \App\Models\User::where('email', 'admin@mali.com')->first();
Auth::login($admin);

$isAdmin = $admin->email === 'admin@mali.com';
echo "User: {$admin->first_name}, Is Admin: " . ($isAdmin ? 'Yes' : 'No') . "\n";

// Simulate DashboardController logic
$currentMonth = now()->month;
$currentYear = now()->year;

echo "Current Month: {$currentMonth}, Year: {$currentYear}\n";

// Test the exact query logic from DashboardController
$transactionQuery = \App\Models\Transaction::query();
if (!$isAdmin) {
    $transactionQuery->where('created_by', Auth::id());
}

echo "Query conditions applied: " . (!$isAdmin ? "created_by = " . Auth::id() : "No user filter (admin)") . "\n";

$monthlyIncome = $transactionQuery->where('type', 'income')
    ->whereMonth('date', $currentMonth)
    ->whereYear('date', $currentYear)
    ->sum('amount');

echo "Monthly Income: {$monthlyIncome}\n";

// THE PROBLEM: The same query object is being reused!
// After the income query, the query already has type='income' filter
// So when we add type='expense', it conflicts

// Let's test this theory
$transactionQuery2 = \App\Models\Transaction::query();
if (!$isAdmin) {
    $transactionQuery2->where('created_by', Auth::id());
}

$monthlyExpenses = $transactionQuery2->where('type', 'expense')
    ->whereMonth('date', $currentMonth)
    ->whereYear('date', $currentYear)
    ->sum('amount');

echo "Monthly Expenses (with fresh query): {$monthlyExpenses}\n";

// Now test the broken way (reusing same query object)
$transactionQuery3 = \App\Models\Transaction::query();
if (!$isAdmin) {
    $transactionQuery3->where('created_by', Auth::id());
}

$monthlyIncomeBroken = $transactionQuery3->where('type', 'income')
    ->whereMonth('date', $currentMonth)
    ->whereYear('date', $currentYear)
    ->sum('amount');

$monthlyExpensesBroken = $transactionQuery3->where('type', 'expense')  // This conflicts with previous type='income'
    ->whereMonth('date', $currentMonth)
    ->whereYear('date', $currentYear)
    ->sum('amount');

echo "Monthly Income (reused query): {$monthlyIncomeBroken}\n";
echo "Monthly Expenses (reused query): {$monthlyExpensesBroken}\n";

echo "\n=== ANALYSIS ===\n";
echo "The issue is query reuse! After setting type='income', adding type='expense' returns 0.\n";
echo "Solution: Use separate query objects for income and expenses.\n";
