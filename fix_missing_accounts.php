<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== FIXING MISSING ACCOUNTS ===\n";

// Get all users
$users = \App\Models\User::all();

foreach ($users as $user) {
    echo "\nUser: " . $user->first_name . " " . $user->last_name . " (ID: " . $user->id . ")\n";
    
    // Check if user has Cash on Hand account
    $cashAccount = \App\Models\Account::where('user_id', $user->id)
        ->where('name', 'Cash on Hand')
        ->first();
    
    if (!$cashAccount) {
        echo "  Creating missing Cash on Hand account...\n";
        \App\Models\Account::create([
            'user_id' => $user->id,
            'name' => 'Cash on Hand',
            'balance' => 0
        ]);
    } else {
        echo "  Cash on Hand account exists: Balance " . $cashAccount->balance . "\n";
    }
    
    // Check if user has HesabPay account
    $hesabPayAccount = \App\Models\Account::where('user_id', $user->id)
        ->where('name', 'HesabPay')
        ->first();
    
    if (!$hesabPayAccount) {
        echo "  Creating missing HesabPay account...\n";
        \App\Models\Account::create([
            'user_id' => $user->id,
            'name' => 'HesabPay',
            'balance' => 0
        ]);
    } else {
        echo "  HesabPay account exists: Balance " . $hesabPayAccount->balance . "\n";
    }
}

echo "\n=== ACCOUNTS FIXED ===\n";

// Show final state
echo "\nFinal account state:\n";
$allAccounts = \App\Models\Account::with('user')->get();
foreach ($allAccounts as $account) {
    echo "ID: " . $account->id . " - " . $account->name . ": " . $account->balance . " (User: " . ($account->user ? $account->user->first_name : 'NULL') . ")\n";
}
