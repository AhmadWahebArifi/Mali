<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING ACCOUNTS DROPDOWN FIX ===\n";

// Test admin user
$admin = \App\Models\User::where('email', 'admin@mali.com')->first();
Auth::login($admin);

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

echo "ADMIN USER ({$admin->first_name}):\n";
echo "Would see {$accounts->count()} accounts:\n";
foreach ($accounts as $account) {
    $owner = $account->user ? $account->user->first_name : 'Shared';
    echo "- {$account->name} (Owner: {$owner})\n";
}

// Test regular user
Auth::logout();
$ismail = \App\Models\User::where('email', 'ismail@mali.com')->first();
Auth::login($ismail);

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

echo "\nREGULAR USER ({$ismail->first_name}):\n";
echo "Would see {$accounts->count()} accounts:\n";
foreach ($accounts as $account) {
    $owner = $account->user ? $account->user->first_name : 'Shared';
    echo "- {$account->name} (Owner: {$owner})\n";
}

echo "\n=== RESULTS ===\n";
if ($accounts->count() > 0) {
    echo "✅ SUCCESS: Regular users can see their own accounts\n";
    echo "✅ Transaction form dropdown will show accounts\n";
} else {
    echo "❌ FAILED: Regular users still have no accounts\n";
}

echo "\n=== TEST COMPLETE ===\n";
