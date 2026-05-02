<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ACCOUNT BALANCES ===\n";
$accounts = \App\Models\Account::with('user')->get();
$totalBalance = 0;

foreach ($accounts as $account) {
    $userName = $account->user ? ($account->user->first_name . " " . $account->user->last_name) : "User ID: " . $account->user_id . " (NULL)";
    echo "User: " . $userName . 
         ", Account: " . $account->name . 
         ", Balance: " . $account->balance . "\n";
    
    if ($account->user_id == 1) { // Assuming user 1 is the logged-in user
        $totalBalance += $account->balance;
    }
}

echo "\nUser 1 Total Balance: " . $totalBalance . "\n";

echo "\n=== RECENT TRANSACTIONS ===\n";
$transactions = \App\Models\Transaction::with(['account', 'creator'])
    ->orderBy('created_at', 'desc')
    ->take(10)
    ->get();

foreach ($transactions as $t) {
    echo "ID: {$t->id}, Type: {$t->type}, Amount: {$t->amount}, " .
         "Account: {$t->account->name}, " .
         "By: {$t->creator->first_name} {$t->creator->last_name}, " .
         "Date: {$t->date}\n";
}

echo "\n=== MONTHLY SUMMARY ===\n";
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

echo "Monthly Income: " . $monthlyIncome . "\n";
echo "Monthly Expenses: " . $monthlyExpenses . "\n";
echo "Net Monthly: " . ($monthlyIncome - $monthlyExpenses) . "\n";
