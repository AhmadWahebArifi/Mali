<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CLEANING UP GLOBAL ACCOUNTS ===\n";

// Remove old Global Cash Account
$deleted = \App\Models\Account::where('name', 'Global Cash Account')->delete();
echo "Deleted old Global Cash Account: $deleted records\n";

// Show current global accounts
$globalAccounts = \App\Models\Account::whereNull('user_id')->get();
echo "\nCurrent global accounts:\n";

foreach ($globalAccounts as $account) {
    echo "- {$account->name}: {$account->balance}\n";
}

echo "\nTotal global accounts: " . $globalAccounts->count() . "\n";
