<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUGGING ASSIGNMENT CALCULATION ===\n";

// Get the latest budget assignment
$assignment = \App\Models\BudgetAssignment::with(['budget', 'account', 'user'])
    ->orderBy('created_at', 'desc')
    ->first();

if (!$assignment) {
    echo "No budget assignment found.\n";
    exit;
}

echo "Assignment ID: {$assignment->id}\n";
echo "Budget ID: {$assignment->budget_id}\n";
echo "Budget Name: {$assignment->budget->name}\n";
echo "Assigned Amount: {$assignment->assigned_amount}\n";
echo "Remaining Amount: {$assignment->remaining_amount}\n";
echo "Spent Amount: {$assignment->spent_amount}\n";

// Check transactions for this budget
$transactions = \App\Models\Transaction::where('budget_id', $assignment->budget_id)
    ->where('type', 'expense')
    ->get();

echo "\nTransactions for Budget ID {$assignment->budget_id}:\n";
if ($transactions->isEmpty()) {
    echo "No transactions found for this budget.\n";
} else {
    foreach ($transactions as $transaction) {
        echo "- ID: {$transaction->id}, Amount: {$transaction->amount}, Description: {$transaction->description}\n";
    }
}

// Calculate spent amount manually
$manualSpent = \App\Models\Transaction::where('budget_id', $assignment->budget_id)
    ->where('type', 'expense')
    ->sum('amount');

echo "\nManual calculation:\n";
echo "Total spent from budget: {$manualSpent}\n";
echo "Expected remaining: " . ($assignment->assigned_amount - $manualSpent) . "\n";

// Check if the transaction was created with the correct budget_id
$latestTransaction = \App\Models\Transaction::orderBy('created_at', 'desc')->first();
if ($latestTransaction) {
    echo "\nLatest transaction:\n";
    echo "- ID: {$latestTransaction->id}\n";
    echo "- Budget ID: " . ($latestTransaction->budget_id ?? 'NULL') . "\n";
    echo "- Amount: {$latestTransaction->amount}\n";
    echo "- Type: {$latestTransaction->type}\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
