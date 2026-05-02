<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ALL ACCOUNTS IN DATABASE ===\n";
$accounts = \App\Models\Account::with('user')->get();

foreach ($accounts as $account) {
    $userName = $account->user ? ($account->user->first_name . " " . $account->user->last_name) : "User ID: " . $account->user_id . " (NULL)";
    echo "ID: {$account->id}, Name: {$account->name}, Balance: {$account->balance}, User: {$userName}\n";
}

echo "\n=== USER 1 (Admin) ACCOUNTS ===\n";
$user1Accounts = \App\Models\Account::where('user_id', 1)->get();
$totalBalance = 0;

foreach ($user1Accounts as $account) {
    echo "Account: {$account->name}, Balance: {$account->balance}\n";
    $totalBalance += $account->balance;
}

echo "\nUser 1 Total Balance: {$totalBalance}\n";

echo "\n=== CURRENT DASHBOARD CALCULATION ===\n";
$accountQuery = \App\Models\Account::where('user_id', 1);
$totalBalance = $accountQuery->sum('balance');
echo "Dashboard calculates: {$totalBalance}\n";
