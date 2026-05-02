<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING DASHBOARD ACCOUNT DISPLAY ===\n";

// Test what each user sees on their dashboard
$users = \App\Models\User::all();

foreach ($users as $user) {
    echo "\n--- User: " . $user->first_name . " " . $user->last_name . " (ID: " . $user->id . ") ---\n";
    
    // Simulate the dashboard query for this user
    $accounts = \App\Models\Account::whereIn('name', ['Cash on Hand', 'HesabPay'])
        ->where('user_id', $user->id)
        ->orderBy('name')
        ->get();
    
    echo "Accounts shown on dashboard:\n";
    if ($accounts->isEmpty()) {
        echo "  No accounts found\n";
    } else {
        foreach ($accounts as $account) {
            echo "  - " . $account->name . ": $" . $account->balance . "\n";
        }
    }
}

echo "\n=== TESTING COMPLETE ===\n";
