<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUGGING CURRENT ACCOUNTS ===\n";

// Get all accounts
$allAccounts = \App\Models\Account::with('user')->orderBy('name')->get();

echo "All Accounts in System:\n";
foreach ($allAccounts as $account) {
    echo "- ID: {$account->id}, Name: {$account->name}, User: {$account->user->first_name}, Balance: {$account->balance}\n";
}

// Check for duplicate Cash on Hand accounts
$cashOnHandAccounts = \App\Models\Account::where('name', 'Cash on Hand')->get();
echo "\nCash on Hand Accounts ({$cashOnHandAccounts->count()} total):\n";
foreach ($cashOnHandAccounts as $account) {
    echo "- ID: {$account->id}, User: {$account->user->first_name}, Balance: {$account->balance}\n";
}

// Check for duplicate HesabPay accounts
$hesabPayAccounts = \App\Models\Account::where('name', 'HesabPay')->get();
echo "\nHesabPay Accounts ({$hesabPayAccounts->count()} total):\n";
foreach ($hesabPayAccounts as $account) {
    echo "- ID: {$account->id}, User: {$account->user->first_name}, Balance: {$account->balance}\n";
}

// Test the firstOrCreate logic
echo "\n=== TESTING firstOrCreate LOGIC ===\n";
$ismail = \App\Models\User::where('email', 'ismail@mali.com')->first();

echo "Looking for 'Cash on Hand' account for Ismail (ID: {$ismail->id})...\n";
$existingAccount = \App\Models\Account::where('user_id', $ismail->id)
    ->where('name', 'Cash on Hand')
    ->first();

if ($existingAccount) {
    echo "✓ Found existing account: ID {$existingAccount->id}\n";
} else {
    echo "✗ No existing account found - will create new one\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
