<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING SIMPLE TRANSACTION CREATION ===\n";

// Get test data
$ismail = \App\Models\User::where('email', 'ismail@mali.com')->first();
$category = \App\Models\Category::first();
$account = \App\Models\Account::where('user_id', $ismail->id)
    ->where('name', 'Cash on Hand')
    ->first();
$budget = \App\Models\Budget::where('user_id', $ismail->id)->first();

echo "User: {$ismail->first_name} (ID: {$ismail->id})\n";
echo "Category: {$category->name} (ID: {$category->id})\n";
echo "Account: {$account->name} (ID: {$account->id})\n";
echo "Budget: {$budget->name} (ID: {$budget->id})\n";

// Create transaction with minimal data
echo "\nCreating transaction...\n";

$transactionData = [
    'type' => 'expense',
    'amount' => 10.00,
    'category_id' => $category->id,
    'account_id' => $account->id,
    'budget_id' => $budget->id,
    'date' => date('Y-m-d'),
    'description' => 'Simple test',
    'created_by' => $ismail->id,
];

echo "Transaction data:\n";
foreach ($transactionData as $key => $value) {
    echo "- {$key}: {$value}\n";
}

$transaction = \App\Models\Transaction::create($transactionData);

echo "\nCreated transaction:\n";
echo "- ID: {$transaction->id}\n";
echo "- Budget ID: " . ($transaction->budget_id ?? 'NULL') . "\n";
echo "- Amount: {$transaction->amount}\n";

// Refresh from database to ensure we have the latest data
$transaction->refresh();
echo "- Refreshed Budget ID: " . ($transaction->budget_id ?? 'NULL') . "\n";

// Check database directly
$dbTransaction = \App\Models\Transaction::find($transaction->id);
echo "- Database Budget ID: " . ($dbTransaction->budget_id ?? 'NULL') . "\n";

echo "\n=== TEST COMPLETE ===\n";
