<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ALL USERS AND THEIR ACCOUNTS ===\n";
$users = \App\Models\User::all();

foreach ($users as $user) {
    echo "\nUser ID: {$user->id}, Name: {$user->first_name} {$user->last_name}, Email: {$user->email}\n";
    
    $accounts = \App\Models\Account::where('user_id', $user->id)->get();
    echo "  Account count: " . $accounts->count() . "\n";
    
    $totalBalance = 0;
    foreach ($accounts as $account) {
        echo "    - {$account->name}: {$account->balance}\n";
        $totalBalance += $account->balance;
    }
    
    echo "  Total balance: {$totalBalance}\n";
}

echo "\n=== CREATING ACCOUNTS FOR USERS WITHOUT ACCOUNTS ===\n";
foreach ($users as $user) {
    $accountCount = \App\Models\Account::where('user_id', $user->id)->count();
    if ($accountCount == 0) {
        echo "Creating accounts for User {$user->id}: {$user->first_name} {$user->last_name}\n";
        
        \App\Models\Account::create([
            'name' => 'Cash on Hand',
            'balance' => 25000,
            'user_id' => $user->id
        ]);
        
        \App\Models\Account::create([
            'name' => 'HesabPay',
            'balance' => 15000,
            'user_id' => $user->id
        ]);
        
        echo "  Created Cash on Hand (25,000) and HesabPay (15,000)\n";
    }
}
