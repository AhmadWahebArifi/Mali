<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CHECKING ISMAIL'S ACCOUNTS ===\n";

// Get Ismail user
$user = \App\Models\User::find(3);
if (!$user) {
    echo "User ID 3 not found!\n";
    exit;
}

echo "User: " . $user->first_name . " " . $user->last_name . " (ID: " . $user->id . ")\n";

// Get Ismail's accounts
$accounts = \App\Models\Account::where('user_id', $user->id)->get();
echo "\nIsmail's accounts:\n";
foreach ($accounts as $account) {
    echo "- Account ID: " . $account->id . ", Name: '" . $account->name . "', Balance: " . $account->balance . "\n";
}

// Check if there are multiple Cash on Hand accounts
$cashOnHandAccounts = \App\Models\Account::where('user_id', $user->id)
    ->where('name', 'Cash on Hand')
    ->get();

echo "\nCash on Hand accounts for Ismail: " . $cashOnHandAccounts->count() . "\n";
foreach ($cashOnHandAccounts as $account) {
    echo "- ID: " . $account->id . ", Balance: " . $account->balance . "\n";
}

echo "\n=== CHECK COMPLETE ===\n";
