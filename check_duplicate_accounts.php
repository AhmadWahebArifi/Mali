<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CHECKING FOR DUPLICATE ACCOUNTS ===\n";

// Get all accounts grouped by user and name
$accounts = \App\Models\Account::with('user')->get();

$groupedAccounts = [];
foreach ($accounts as $account) {
    $key = ($account->user_id ? $account->user_id : 'NULL') . '_' . $account->name;
    if (!isset($groupedAccounts[$key])) {
        $groupedAccounts[$key] = [];
    }
    $groupedAccounts[$key][] = $account;
}

echo "Found " . count($accounts) . " total accounts\n\n";

// Check for duplicates
$duplicatesFound = false;
foreach ($groupedAccounts as $key => $accountGroup) {
    if (count($accountGroup) > 1) {
        $duplicatesFound = true;
        echo "DUPLICATE FOUND: $key\n";
        foreach ($accountGroup as $account) {
            echo "  - ID: {$account->id}, Balance: {$account->balance}, User: " . ($account->user ? $account->user->first_name : 'NULL') . "\n";
        }
        echo "\n";
    }
}

if (!$duplicatesFound) {
    echo "No duplicate accounts found.\n\n";
    
    // Show all accounts for debugging
    echo "All accounts:\n";
    foreach ($accounts as $account) {
        echo "ID: {$account->id}, Name: '{$account->name}', Balance: {$account->balance}, User ID: " . ($account->user_id ?? 'NULL') . ", User: " . ($account->user ? $account->user->first_name : 'NULL') . "\n";
    }
}

echo "=== CHECK COMPLETE ===\n";
