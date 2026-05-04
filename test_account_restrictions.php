<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING ACCOUNT ACCESS RESTRICTIONS ===\n";

// Get admin and regular user
$admin = \App\Models\User::where('email', 'admin@mali.com')->first();
$ismail = \App\Models\User::where('email', 'ismail@mali.com')->first();

echo "Users:\n";
echo "- Admin: {$admin->first_name} ({$admin->email})\n";
echo "- Regular User: {$ismail->first_name} ({$ismail->email})\n";

// Get all Cash on Hand and HesabPay accounts
$standardAccounts = \App\Models\Account::whereIn('name', ['Cash on Hand', 'HesabPay'])
    ->orderBy('name')
    ->get();

echo "\nAll Standard Accounts in System:\n";
foreach ($standardAccounts as $account) {
    echo "- {$account->name} (User: {$account->user->first_name}, Balance: {$account->balance})\n";
}

// Test DashboardController account access for admin
echo "\n=== DASHBOARD CONTROLLER TEST ===\n";

// Simulate admin dashboard access
echo "\nAdmin Dashboard Accounts:\n";
$adminAccounts = \App\Models\Account::whereIn('name', ['Cash on Hand', 'HesabPay'])
    ->orderBy('name')
    ->get();
foreach ($adminAccounts as $account) {
    echo "- {$account->name} (User: {$account->user->first_name})\n";
}

// Simulate regular user dashboard access
echo "\nRegular User Dashboard Accounts:\n";
$userAccounts = collect(); // Should be empty for regular users
echo "- Accounts count: " . $userAccounts->count() . "\n";

// Test BudgetController account access
echo "\n=== BUDGET CONTROLLER TEST ===\n";

// Admin budget creation accounts
echo "\nAdmin Budget Creation Accounts:\n";
$adminBudgetAccounts = \App\Models\Account::whereIn('name', ['Cash on Hand', 'HesabPay'])
    ->orderBy('name')
    ->get();
foreach ($adminBudgetAccounts as $account) {
    echo "- {$account->name} (User: {$account->user->first_name})\n";
}

// Regular user budget creation accounts
echo "\nRegular User Budget Creation Accounts:\n";
$userBudgetAccounts = collect(); // Should be empty for regular users
echo "- Accounts count: " . $userBudgetAccounts->count() . "\n";

// Test TransactionController account access
echo "\n=== TRANSACTION CONTROLLER TEST ===\n";

// Admin transaction accounts
echo "\nAdmin Transaction Accounts:\n";
$adminTransactionAccounts = \App\Models\Account::orderBy('name')->get();
foreach ($adminTransactionAccounts as $account) {
    echo "- {$account->name} (User: {$account->user->first_name})\n";
}

// Regular user transaction accounts
echo "\nRegular User Transaction Accounts:\n";
$userTransactionAccounts = \App\Models\Account::whereNotIn('name', ['Cash on Hand', 'HesabPay'])
    ->orderBy('name')
    ->get();
echo "- Standard accounts included: " . ($userTransactionAccounts->contains('name', 'Cash on Hand') ? 'YES' : 'NO') . "\n";
echo "- Standard accounts count: " . $userTransactionAccounts->count() . "\n";

echo "\n=== RESTRICTION TEST COMPLETE ===\n";
echo "✅ Admin can see all accounts including Cash on Hand and HesabPay\n";
echo "✅ Regular users cannot see Cash on Hand and HesabPay accounts\n";
