<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ALL ACCOUNTS IN DATABASE ===\n";
$allAccounts = \App\Models\Account::with('user')->get();

foreach ($allAccounts as $account) {
    $userName = $account->user ? $account->user->first_name : "NULL";
    echo "ID: {$account->id}, Name: '{$account->name}', Balance: {$account->balance}, User ID: " . ($account->user_id ?? 'NULL') . ", User: {$userName}\n";
}

echo "\n=== GLOBAL ACCOUNTS (user_id = null) ===\n";
$globalAccounts = \App\Models\Account::whereNull('user_id')->get();
foreach ($globalAccounts as $account) {
    echo "Name: '{$account->name}', Balance: {$account->balance}\n";
}

echo "\nTotal global accounts: " . $globalAccounts->count() . "\n";
