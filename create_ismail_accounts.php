<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get Ismail user
$user = \App\Models\User::where('email', 'ismail@mali.com')->first();

if (!$user) {
    echo "User Ismail Ahmadi not found!\n";
    exit;
}

// Create Cash on Hand account
$cashAccount = \App\Models\Account::firstOrCreate(
    ['user_id' => $user->id, 'name' => 'Cash on Hand'],
    ['balance' => 0]
);

// Create HesabPay account
$hesabPayAccount = \App\Models\Account::firstOrCreate(
    ['user_id' => $user->id, 'name' => 'HesabPay'],
    ['balance' => 0]
);

echo "Accounts created for Ismail Ahmadi:\n";
echo "- Cash on Hand: {$cashAccount->balance}\n";
echo "- HesabPay: {$hesabPayAccount->balance}\n";
