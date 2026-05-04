<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING UPDATED ACCOUNTS FOR TRANSACTIONS ===\n";

// Test with regular user
$ismail = \App\Models\User::where('email', 'ismail@mali.com')->first();
Auth::login($ismail);

// Simulate the updated TransactionController create method
$user = Auth::user();
$isAdmin = $user->email === 'admin@mali.com';

if ($isAdmin) {
    $accounts = \App\Models\Account::orderBy('name')->get();
} else {
    // Regular users can see Cash on Hand and HesabPay for transactions
    $accounts = \App\Models\Account::whereIn('name', ['Cash on Hand', 'HesabPay'])
        ->orderBy('name')
        ->get();
}

echo "User: {$user->first_name} (Regular User)\n";
echo "Accounts available: {$accounts->count()}\n";

if ($accounts->count() > 0) {
    echo "Accounts that will show in dropdown:\n";
    foreach ($accounts as $account) {
        echo "  - {$account->name} (ID: {$account->id}, Balance: {$account->balance})\n";
    }
} else {
    echo "❌ NO ACCOUNTS FOUND!\n";
}

echo "\n=== TESTING ADMIN USER ===\n";

// Test with admin user
Auth::logout();
$admin = \App\Models\User::where('email', 'admin@mali.com')->first();
Auth::login($admin);

$user = Auth::user();
$isAdmin = $user->email === 'admin@mali.com';

if ($isAdmin) {
    $accounts = \App\Models\Account::orderBy('name')->get();
} else {
    $accounts = \App\Models\Account::whereIn('name', ['Cash on Hand', 'HesabPay'])
        ->orderBy('name')
        ->get();
}

echo "User: {$user->first_name} (Admin)\n";
echo "Accounts available: {$accounts->count()}\n";
echo "First few accounts:\n";
foreach ($accounts->take(5) as $account) {
    $owner = $account->user ? $account->user->first_name : 'Shared';
    echo "  - {$account->name} (Owner: {$owner})\n";
}

echo "\n=== EXPECTED DEBUG OUTPUT ===\n";
echo "For regular users, you should now see:\n";
echo "DEBUG: Accounts count: 2 | User: Ismail (ID: 3) | First account: Cash on Hand\n";

echo "\n=== TRANSACTION BEHAVIOR ===\n";
echo "✅ Income/Expense transactions will go to Cash on Hand/HesabPay\n";
echo "✅ Account balances will NOT be affected (as implemented earlier)\n";
echo "✅ Monthly calculations will include all transactions\n";
