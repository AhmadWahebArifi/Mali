<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING MONEY PRECISION ===\n";

// Test 1: Direct PHP floating point arithmetic
echo "1. Direct PHP arithmetic:\n";
$amount = 1000.00;
echo "Input: $amount\n";
echo "Formatted: " . number_format($amount, 2) . "\n\n";

// Test 2: Database decimal precision
echo "2. Database decimal test:\n";
$account = new \App\Models\Account();
$account->name = 'Test Account';
$account->balance = 1000.00;
$account->user_id = null;
$account->save();

echo "Saved balance: " . $account->balance . "\n";
echo "Formatted: " . number_format($account->balance, 2) . "\n";

// Test 3: Account balance update
echo "3. Account balance update test:\n";
$account->balance += 0.02;
$account->save();
echo "After adding 0.02: " . $account->balance . "\n";
echo "Formatted: " . number_format($account->balance, 2) . "\n";

$account->balance -= 0.02;
$account->save();
echo "After subtracting 0.02: " . $account->balance . "\n";
echo "Formatted: " . number_format($account->balance, 2) . "\n";

// Test 4: Small amounts
echo "4. Small amounts test:\n";
$smallAccount = new \App\Models\Account();
$smallAccount->name = 'Small Test';
$smallAccount->balance = 0.02;
$smallAccount->user_id = null;
$smallAccount->save();
echo "Small amount saved: " . $smallAccount->balance . "\n";
echo "Formatted: " . number_format($smallAccount->balance, 2) . "\n";

// Cleanup
$account->delete();
$smallAccount->delete();

echo "\n=== FormatHelper test ===\n";
echo "FormatHelper 1000: " . \App\Helpers\FormatHelper::currency(1000) . "\n";
echo "FormatHelper 0.02: " . \App\Helpers\FormatHelper::currency(0.02) . "\n";
echo "FormatHelper 999.98: " . \App\Helpers\FormatHelper::currency(999.98) . "\n";
