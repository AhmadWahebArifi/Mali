<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== USER 1 (ADMIN) CHECK ===\n";
$user = \App\Models\User::find(1);
echo "User: " . $user->first_name . " " . $user->last_name . " (" . $user->email . ")\n";

$accounts = \App\Models\Account::where('user_id', 1)->get();
echo "Account count: " . $accounts->count() . "\n";

$totalBalance = 0;
foreach ($accounts as $account) {
    echo "Account: " . $account->name . ", Balance: " . $account->balance . "\n";
    $totalBalance += $account->balance;
}

echo "Total balance: " . $totalBalance . "\n";

echo "\n=== DASHBOARD CALCULATION SIMULATION ===\n";
$accountQuery = \App\Models\Account::where('user_id', 1);
$calculatedBalance = $accountQuery->sum('balance');
echo "Dashboard would calculate: " . $calculatedBalance . "\n";

echo "\n=== CHECK FOR DUPLICATE ACCOUNT NAMES ===\n";
$allAccounts = \App\Models\Account::with('user')->get();
foreach ($allAccounts as $account) {
    $userName = $account->user ? $account->user->first_name : "NULL";
    echo "ID: " . $account->id . ", Name: " . $account->name . ", User: " . $userName . ", Balance: " . $account->balance . "\n";
}
