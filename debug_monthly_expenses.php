<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUGGING MONTHLY EXPENSES CALCULATION ===\n";

// Get admin user
$admin = \App\Models\User::where('email', 'admin@mali.com')->first();
echo "Admin User: {$admin->first_name} (ID: {$admin->id})\n";

// Check all transactions
$allTransactions = \App\Models\Transaction::with(['creator', 'category', 'account'])->get();
echo "\nAll Transactions ({$allTransactions->count()} total):\n";
foreach ($allTransactions as $transaction) {
    echo "- ID: {$transaction->id}, Type: {$transaction->type}, Amount: {$transaction->amount}, User: {$transaction->creator->first_name}, Category: {$transaction->category->name}, Date: {$transaction->date}\n";
}

// Check current month transactions
$currentMonth = date('Y-m');
echo "\nCurrent Month: {$currentMonth}\n";

$currentMonthTransactions = \App\Models\Transaction::with(['creator', 'category'])->where('date', 'like', $currentMonth . '%')->get();
echo "Current Month Transactions ({$currentMonthTransactions->count()} total):\n";
foreach ($currentMonthTransactions as $transaction) {
    echo "- ID: {$transaction->id}, Type: {$transaction->type}, Amount: {$transaction->amount}, User: {$transaction->creator->first_name}, Category: {$transaction->category->name}, Date: {$transaction->date}\n";
}

// Calculate monthly expenses (expense transactions only)
$monthlyExpenses = \App\Models\Transaction::where('type', 'expense')
    ->where('date', 'like', $currentMonth . '%')
    ->sum('amount');

echo "\nCalculated Monthly Expenses: {$monthlyExpenses}\n";

// Check if there are any expense transactions this month
$expenseTransactions = \App\Models\Transaction::with(['creator'])
    ->where('type', 'expense')
    ->where('date', 'like', $currentMonth . '%')
    ->get();

echo "Expense Transactions This Month:\n";
foreach ($expenseTransactions as $transaction) {
    echo "- ID: {$transaction->id}, Amount: {$transaction->amount}, User: {$transaction->creator->first_name}, Date: {$transaction->date}\n";
}

// Check DashboardController logic
echo "\n=== DASHBOARD CONTROLLER LOGIC ===\n";

// Simulate DashboardController monthly expenses calculation
$user = Auth::user();
if ($user) {
    echo "Logged in user: {$user->first_name}\n";
    
    $monthlyExpenses = \App\Models\Transaction::where('user_id', $user->id)
        ->where('type', 'expense')
        ->whereMonth('date', now()->month)
        ->whereYear('date', now()->year)
        ->sum('amount');
    
    echo "User-specific monthly expenses: {$monthlyExpenses}\n";
} else {
    echo "No user logged in - checking global expenses\n";
    
    $globalMonthlyExpenses = \App\Models\Transaction::where('type', 'expense')
        ->whereMonth('date', now()->month)
        ->whereYear('date', now()->year)
        ->sum('amount');
    
    echo "Global monthly expenses: {$globalMonthlyExpenses}\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
