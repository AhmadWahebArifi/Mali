<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING TRANSACTION PRECISION ===\n";

// Get global accounts
$cashAccount = \App\Models\Account::where('name', 'Cash on Hand')->whereNull('user_id')->first();
$hesabPayAccount = \App\Models\Account::where('name', 'HesabPay')->whereNull('user_id')->first();

if (!$cashAccount) {
    echo "Cash on Hand account not found!\n";
    exit;
}

echo "Starting balance: " . $cashAccount->balance . "\n";

// Test 1: Add 1000.00
echo "\n1. Adding 1000.00...\n";
$cashAccount->balance += 1000.00;
$cashAccount->save();
echo "Balance after adding 1000: " . $cashAccount->balance . "\n";
echo "Formatted: " . number_format($cashAccount->balance, 2) . "\n";

// Test 2: Subtract 0.02
echo "\n2. Subtracting 0.02...\n";
$cashAccount->balance -= 0.02;
$cashAccount->save();
echo "Balance after subtracting 0.02: " . $cashAccount->balance . "\n";
echo "Formatted: " . number_format($cashAccount->balance, 2) . "\n";

// Test 3: Create transaction with 1000.00
echo "\n3. Creating transaction with 1000.00...\n";
$transaction = new \App\Models\Transaction();
$transaction->type = 'income';
$transaction->amount = 1000.00;
$transaction->account_id = $cashAccount->id;
$transaction->category_id = 1; // Assuming category 1 exists
$transaction->description = 'Test transaction 1000.00';
$transaction->date = date('Y-m-d');
$transaction->created_by = 1; // Admin user
$transaction->save();

echo "Transaction amount saved: " . $transaction->amount . "\n";
echo "Account balance after transaction: " . $cashAccount->fresh()->balance . "\n";
echo "Formatted: " . number_format($cashAccount->fresh()->balance, 2) . "\n";

// Test 4: Create transaction with 0.02
echo "\n4. Creating transaction with 0.02...\n";
$smallTransaction = new \App\Models\Transaction();
$smallTransaction->type = 'income';
$smallTransaction->amount = 0.02;
$smallTransaction->account_id = $cashAccount->id;
$smallTransaction->category_id = 1;
$smallTransaction->description = 'Test transaction 0.02';
$smallTransaction->date = date('Y-m-d');
$smallTransaction->created_by = 1;
$smallTransaction->save();

echo "Small transaction amount saved: " . $smallTransaction->amount . "\n";
echo "Account balance after small transaction: " . $cashAccount->fresh()->balance . "\n";
echo "Formatted: " . number_format($cashAccount->fresh()->balance, 2) . "\n";

// Test 5: Check database sum
echo "\n5. Testing database SUM...\n";
$sum = \App\Models\Transaction::where('account_id', $cashAccount->id)->sum('amount');
echo "Database SUM of transactions: " . $sum . "\n";
echo "Formatted: " . number_format($sum, 2) . "\n";

// Cleanup
$transaction->delete();
$smallTransaction->delete();
$cashAccount->balance = 0;
$cashAccount->save();

echo "\n=== Test completed ===\n";
