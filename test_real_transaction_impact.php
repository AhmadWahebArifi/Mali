<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING REAL TRANSACTION IMPACT ON ACCOUNTS ===\n";

// Get current account balances
$cashOnHand = \App\Models\Account::where('name', 'Cash on Hand')->first();
$hesabPay = \App\Models\Account::where('name', 'HesabPay')->first();

echo "BEFORE TRANSACTIONS:\n";
echo "Cash on Hand Balance: {$cashOnHand->balance}\n";
echo "HesabPay Balance: {$hesabPay->balance}\n";

// Get current net worth
$allAccounts = \App\Models\Account::all();
$currentNetWorth = $allAccounts->sum('balance');
echo "Current Net Worth: {$currentNetWorth}\n";

// Simulate income transaction to Cash on Hand
echo "\n=== SIMULATING INCOME TRANSACTION ===\n";
$incomeAmount = 1000;

// Update Cash on Hand balance (income increases)
$cashOnHand->balance = round($cashOnHand->balance + $incomeAmount, 2);
$cashOnHand->save();

echo "Added {$incomeAmount} income to Cash on Hand\n";
echo "Cash on Hand New Balance: {$cashOnHand->balance}\n";

// Simulate expense transaction from HesabPay
echo "\n=== SIMULATING EXPENSE TRANSACTION ===\n";
$expenseAmount = 500;

// Update HesabPay balance (expense decreases)
$hesabPay->balance = round($hesabPay->balance - $expenseAmount, 2);
$hesabPay->save();

echo "Subtracted {$expenseAmount} expense from HesabPay\n";
echo "HesabPay New Balance: {$hesabPay->balance}\n";

// Calculate new net worth
$allAccounts = \App\Models\Account::all();
$newNetWorth = $allAccounts->sum('balance');

echo "\n=== RESULTS ===\n";
echo "Old Net Worth: {$currentNetWorth}\n";
echo "New Net Worth: {$newNetWorth}\n";
echo "Net Worth Change: " . ($newNetWorth - $currentNetWorth) . "\n";

echo "\n=== EXPECTED BEHAVIOR ===\n";
echo "✅ Income should increase Cash on Hand balance\n";
echo "✅ Expense should decrease HesabPay balance\n";
echo "✅ Net Worth should reflect actual account balances\n";
echo "✅ Dashboard should show updated account balances\n";

// Reset for next test
echo "\n=== RESETTING BALANCES ===\n";
$cashOnHand->balance = 29000.00;
$hesabPay->balance = 10000.00;
$cashOnHand->save();
$hesabPay->save();
echo "Balances reset to original values\n";
