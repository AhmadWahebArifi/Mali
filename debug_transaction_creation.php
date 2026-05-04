<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUGGING TRANSACTION CREATION ===\n";

// Check the latest transaction
$latestTransaction = \App\Models\Transaction::orderBy('created_at', 'desc')->first();

if ($latestTransaction) {
    echo "Latest Transaction Details:\n";
    echo "- ID: {$latestTransaction->id}\n";
    echo "- Type: {$latestTransaction->type}\n";
    echo "- Amount: {$latestTransaction->amount}\n";
    echo "- Description: {$latestTransaction->description}\n";
    echo "- Budget ID: " . ($latestTransaction->budget_id ?? 'NULL') . "\n";
    echo "- Category ID: {$latestTransaction->category_id}\n";
    echo "- Account ID: {$latestTransaction->account_id}\n";
    echo "- Created By: {$latestTransaction->created_by}\n";
    echo "- Created At: {$latestTransaction->created_at}\n";
} else {
    echo "No transactions found.\n";
}

// Check if the budget exists
$budget = \App\Models\Budget::find(6);
if ($budget) {
    echo "\nBudget ID 6 exists:\n";
    echo "- Name: {$budget->name}\n";
    echo "- Amount: {$budget->amount}\n";
    echo "- User ID: {$budget->user_id}\n";
} else {
    echo "\nBudget ID 6 not found.\n";
}

// Try to create a transaction manually to test
echo "\n=== CREATING TEST TRANSACTION MANUALLY ===\n";

$ismail = \App\Models\User::where('email', 'ismail@mali.com')->first();
$category = \App\Models\Category::first();
$account = \App\Models\Account::where('user_id', $ismail->id)
    ->where('name', 'Cash on Hand')
    ->first();

$testTransaction = \App\Models\Transaction::create([
    'type' => 'expense',
    'amount' => 25.00,
    'category_id' => $category->id,
    'account_id' => $account->id,
    'budget_id' => 6, // Explicitly set budget_id
    'date' => now(),
    'description' => 'Manual test transaction',
    'created_by' => $ismail->id,
    'is_over_budget' => false,
    'outstanding_amount' => 0,
]);

echo "Test transaction created:\n";
echo "- ID: {$testTransaction->id}\n";
echo "- Budget ID: " . ($testTransaction->budget_id ?? 'NULL') . "\n";
echo "- Amount: {$testTransaction->amount}\n";

// Verify it was saved correctly
$verifyTransaction = \App\Models\Transaction::find($testTransaction->id);
echo "- Verified Budget ID: " . ($verifyTransaction->budget_id ?? 'NULL') . "\n";

echo "\n=== DEBUG COMPLETE ===\n";
