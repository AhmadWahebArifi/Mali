<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUGGING USER ACCOUNTS ISSUE ===\n";

// Check all users and their accounts
$users = \App\Models\User::all();
echo "All Users:\n";
foreach ($users as $user) {
    echo "- ID: {$user->id}, Name: {$user->first_name} {$user->last_name}, Email: {$user->email}\n";
}

echo "\n=== ALL ACCOUNTS IN SYSTEM ===\n";
$allAccounts = \App\Models\Account::with('user')->orderBy('name')->get();
foreach ($allAccounts as $account) {
    $owner = $account->user ? $account->user->first_name : 'Shared';
    echo "- ID: {$account->id}, Name: {$account->name}, User ID: {$account->user_id}, Owner: {$owner}, Balance: {$account->balance}\n";
}

echo "\n=== TESTING TRANSACTION CONTROLLER CREATE METHOD ===\n";

// Test with different users
$testUsers = [
    ['email' => 'admin@mali.com', 'type' => 'Admin'],
    ['email' => 'ismail@mali.com', 'type' => 'Regular User'],
    ['email' => 'test@example.com', 'type' => 'Regular User']
];

foreach ($testUsers as $testUser) {
    $user = \App\Models\User::where('email', $testUser['email'])->first();
    if (!$user) {
        echo "User {$testUser['email']} not found\n";
        continue;
    }
    
    echo "\n--- {$testUser['type']}: {$user->first_name} ---\n";
    
    // Simulate TransactionController create method logic
    $isAdmin = $user->email === 'admin@mali.com';
    
    if ($isAdmin) {
        $accounts = \App\Models\Account::orderBy('name')->get();
    } else {
        // Regular users can only see their own accounts (excluding Cash on Hand and HesabPay)
        $accounts = \App\Models\Account::where('user_id', $user->id)
            ->whereNotIn('name', ['Cash on Hand', 'HesabPay'])
            ->orderBy('name')
            ->get();
    }
    
    echo "Would see {$accounts->count()} accounts:\n";
    if ($accounts->count() === 0) {
        echo "❌ NO ACCOUNTS AVAILABLE!\n";
    } else {
        foreach ($accounts as $account) {
            echo "  - {$account->name} (ID: {$account->id})\n";
        }
    }
}

echo "\n=== CHECKING FOR POTENTIAL ISSUES ===\n";

// Check if there are any other restrictions
echo "1. Checking if users have user_id set correctly:\n";
foreach ($allAccounts as $account) {
    if ($account->user_id) {
        $user = \App\Models\User::find($account->user_id);
        if ($user) {
            echo "✓ Account '{$account->name}' belongs to {$user->first_name}\n";
        } else {
            echo "❌ Account '{$account->name}' has invalid user_id: {$account->user_id}\n";
        }
    } else {
        echo "✓ Account '{$account->name}' is shared (user_id = null)\n";
    }
}

echo "\n2. Checking if regular users have the right accounts:\n";
$regularUsers = \App\Models\User::where('email', '!=', 'admin@mali.com')->get();
foreach ($regularUsers as $user) {
    $userAccounts = \App\Models\Account::where('user_id', $user->id)->get();
    echo "User {$user->first_name} has {$userAccounts->count()} accounts:\n";
    foreach ($userAccounts as $account) {
        echo "  - {$account->name}\n";
    }
}

echo "\n=== DEBUG COMPLETE ===\n";
