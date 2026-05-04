<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUGGING REAL DROPDOWN ISSUE ===\n";

// Check all accounts and their assignments
echo "ALL ACCOUNTS IN DATABASE:\n";
$accounts = \App\Models\Account::with('user')->get();
foreach ($accounts as $account) {
    $owner = $account->user ? $account->user->first_name : 'Shared';
    echo "- ID: {$account->id}, Name: '{$account->name}', User ID: {$account->user_id}, Owner: {$owner}\n";
}

echo "\n=== CHECKING USER ACCOUNT ASSIGNMENTS ===\n";

$users = \App\Models\User::where('email', '!=', 'admin@mali.com')->get();
foreach ($users as $user) {
    echo "\nUser: {$user->first_name} (ID: {$user->id})\n";
    
    // Check what accounts this user should have
    $userAccounts = \App\Models\Account::where('user_id', $user->id)->get();
    echo "Accounts with user_id = {$user->id}: {$userAccounts->count()}\n";
    foreach ($userAccounts as $acc) {
        echo "  - '{$acc->name}' (ID: {$acc->id})\n";
    }
    
    // Check what TransactionController would return
    $isAdmin = $user->email === 'admin@mali.com';
    if ($isAdmin) {
        $controllerAccounts = \App\Models\Account::orderBy('name')->get();
    } else {
        $controllerAccounts = \App\Models\Account::where('user_id', $user->id)
            ->whereNotIn('name', ['Cash on Hand', 'HesabPay'])
            ->orderBy('name')
            ->get();
    }
    
    echo "TransactionController would return: {$controllerAccounts->count()} accounts\n";
    foreach ($controllerAccounts as $acc) {
        echo "  - '{$acc->name}' (ID: {$acc->id})\n";
    }
}

echo "\n=== TESTING ACTUAL VIEW RENDERING ===\n";

// Test if the view would render correctly
$testUser = \App\Models\User::where('email', 'ismail@mali.com')->first();
if ($testUser) {
    Auth::login($testUser);
    
    // Get the data that would be passed to view
    $user = Auth::user();
    $isAdmin = $user->email === 'admin@mali.com';
    
    if ($isAdmin) {
        $accounts = \App\Models\Account::orderBy('name')->get();
    } else {
        $accounts = \App\Models\Account::where('user_id', $user->id)
            ->whereNotIn('name', ['Cash on Hand', 'HesabPay'])
            ->orderBy('name')
            ->get();
    }
    
    echo "For user {$testUser->first_name}:\n";
    echo "Accounts count: {$accounts->count()}\n";
    
    if ($accounts->count() > 0) {
        echo "HTML that would be generated:\n";
        foreach ($accounts as $account) {
            echo "  <option value=\"{$account->id}\">{$account->name}</option>\n";
        }
    } else {
        echo "❌ NO ACCOUNTS - Dropdown would be empty!\n";
    }
}

echo "\n=== POSSIBLE ISSUES ===\n";
echo "1. Check if user is actually logged in when accessing the page\n";
echo "2. Check browser console for JavaScript errors\n";
echo "3. Check if the view file is correct\n";
echo "4. Check if there are any middleware blocking access\n";
