<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CREATING USER-SPECIFIC ACCOUNTS ===\n";

// Get all users
$users = \App\Models\User::where('email', '!=', 'admin@mali.com')->get();
echo "Found {$users->count()} regular users\n";

foreach ($users as $user) {
    echo "\nCreating accounts for user: {$user->first_name} {$user->last_name} ({$user->email})\n";
    
    // Check if user already has accounts
    $existingAccounts = \App\Models\Account::where('user_id', $user->id)->get();
    echo "Existing accounts: {$existingAccounts->count()}\n";
    
    if ($existingAccounts->count() === 0) {
        // Create Personal Account for transactions
        $personalAccount = \App\Models\Account::create([
            'name' => 'Personal Account',
            'user_id' => $user->id,
            'balance' => 0.00
        ]);
        echo "✓ Created Personal Account (ID: {$personalAccount->id})\n";
        
        // Create Savings Account
        $savingsAccount = \App\Models\Account::create([
            'name' => 'Savings Account',
            'user_id' => $user->id,
            'balance' => 0.00
        ]);
        echo "✓ Created Savings Account (ID: {$savingsAccount->id})\n";
        
        // Create Checking Account
        $checkingAccount = \App\Models\Account::create([
            'name' => 'Checking Account',
            'user_id' => $user->id,
            'balance' => 0.00
        ]);
        echo "✓ Created Checking Account (ID: {$checkingAccount->id})\n";
    } else {
        echo "- User already has accounts, skipping creation\n";
        foreach ($existingAccounts as $account) {
            echo "  - {$account->name}: {$account->balance}\n";
        }
    }
}

echo "\n=== VERIFICATION ===\n";

// Test what regular users can see now
$ismail = \App\Models\User::where('email', 'ismail@mali.com')->first();
$regularAccounts = \App\Models\Account::whereNotIn('name', ['Cash on Hand', 'HesabPay'])
    ->orderBy('name')
    ->get();

echo "Regular users can now see {$regularAccounts->count()} accounts:\n";
foreach ($regularAccounts as $account) {
    $userName = $account->user ? $account->user->first_name : 'Shared';
    echo "- {$account->name} (Owner: {$userName})\n";
}

// Test what admin can see
$admin = \App\Models\User::where('email', 'admin@mali.com')->first();
$adminAccounts = \App\Models\Account::orderBy('name')->get();
echo "\nAdmin can see {$adminAccounts->count()} accounts:\n";
foreach ($adminAccounts as $account) {
    $userName = $account->user ? $account->user->first_name : 'Shared';
    echo "- {$account->name} (Owner: {$userName})\n";
}

echo "\n=== COMPLETE ===\n";
echo "✅ Regular users now have accounts for transactions\n";
echo "✅ Transaction form dropdown should show accounts\n";
