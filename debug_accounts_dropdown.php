<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUGGING ACCOUNTS DROPDOWN ISSUE ===\n";

// Get all accounts
$allAccounts = \App\Models\Account::with('user')->orderBy('name')->get();
echo "All Accounts in System:\n";
foreach ($allAccounts as $account) {
    echo "- ID: {$account->id}, Name: {$account->name}, User: " . ($account->user ? $account->user->first_name : 'Shared') . ", Balance: {$account->balance}\n";
}

// Test admin user
$admin = \App\Models\User::where('email', 'admin@mali.com')->first();
echo "\n=== ADMIN USER ===\n";
echo "User: {$admin->first_name}\n";
$adminAccounts = \App\Models\Account::orderBy('name')->get();
echo "Admin would see {$adminAccounts->count()} accounts:\n";
foreach ($adminAccounts as $account) {
    echo "- {$account->name}\n";
}

// Test regular user
$ismail = \App\Models\User::where('email', 'ismail@mali.com')->first();
echo "\n=== REGULAR USER (Ismail) ===\n";
echo "User: {$ismail->first_name}\n";
$regularAccounts = \App\Models\Account::whereNotIn('name', ['Cash on Hand', 'HesabPay'])
    ->orderBy('name')
    ->get();
echo "Regular user would see {$regularAccounts->count()} accounts:\n";
foreach ($regularAccounts as $account) {
    echo "- {$account->name}\n";
}

// Check if regular users need their own accounts
echo "\n=== ANALYSIS ===\n";
if ($regularAccounts->count() === 0) {
    echo "❌ ISSUE: Regular users have no accounts to select from!\n";
    echo "❌ SOLUTION: Need to create user-specific accounts for regular users\n";
} else {
    echo "✅ Regular users have accounts available\n";
}

echo "\n=== RECOMMENDATION ===\n";
echo "1. Create user-specific accounts for regular users (Savings, Checking, etc.)\n";
echo "2. OR allow regular users to see Cash on Hand and HesabPay for transactions\n";
echo "3. OR create a 'Personal Account' for each user automatically\n";
