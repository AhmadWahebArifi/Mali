<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING MONTHLY EXPENSES FIX ===\n";

// Simulate admin user
$admin = \App\Models\User::where('email', 'admin@mali.com')->first();
Auth::login($admin);

$isAdmin = $admin->email === 'admin@mali.com';
echo "User: {$admin->first_name}, Is Admin: " . ($isAdmin ? 'Yes' : 'No') . "\n";

// Test the fixed DashboardController logic
$currentMonth = now()->month;
$currentYear = now()->year;

echo "Current Month: {$currentMonth}, Year: {$currentYear}\n";

// Use separate query objects to avoid conflicts (FIXED VERSION)
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

echo "Monthly Income: {$monthlyIncome}\n";
echo "Monthly Expenses: {$monthlyExpenses}\n";

// Verify with direct queries
$directIncome = \App\Models\Transaction::where('type', 'income')
    ->whereMonth('date', $currentMonth)
    ->whereYear('date', $currentYear)
    ->sum('amount');

$directExpenses = \App\Models\Transaction::where('type', 'expense')
    ->whereMonth('date', $currentMonth)
    ->whereYear('date', $currentYear)
    ->sum('amount');

echo "\nDirect Query Verification:\n";
echo "Direct Income: {$directIncome}\n";
echo "Direct Expenses: {$directExpenses}\n";

echo "\n=== RESULTS ===\n";
if ($monthlyIncome == $directIncome && $monthlyExpenses == $directExpenses) {
    echo "✅ SUCCESS: Monthly expenses calculation fixed!\n";
    echo "✅ Monthly Income: {$monthlyIncome}\n";
    echo "✅ Monthly Expenses: {$monthlyExpenses}\n";
} else {
    echo "❌ FAILED: Values don't match\n";
}

echo "\n=== TEST COMPLETE ===\n";
