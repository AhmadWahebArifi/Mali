<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CHECKING CURRENT STATE ===\n";

// Get admin budget pool
$adminPool = \App\Models\AdminBudgetPool::getCurrent();
echo "Admin Budget Pool:\n";
echo "- Total Budget: {$adminPool->total_budget}\n";
echo "- Total Allocated: {$adminPool->total_allocated}\n";
echo "- Available Funds: {$adminPool->available_funds}\n";

// Get Cash on Hand account
$cashAccount = \App\Models\Account::where('name', 'Cash on Hand')->first();
echo "\nCash on Hand Account:\n";
echo "- ID: {$cashAccount->id}\n";
echo "- Balance: {$cashAccount->balance}\n";
echo "- User: " . ($cashAccount->user ? $cashAccount->user->first_name : 'Shared') . "\n";

// Get all budgets
$allBudgets = \App\Models\Budget::all();
echo "\nAll Budgets ({$allBudgets->count()} total):\n";
$totalBudgetAmount = 0;
foreach ($allBudgets as $budget) {
    echo "- {$budget->name}: {$budget->amount} (User: {$budget->user->first_name})\n";
    $totalBudgetAmount += $budget->amount;
}

echo "\nTotal Budget Amount: {$totalBudgetAmount}\n";

echo "\n=== ANALYSIS ===\n";
echo "Cash on Hand should show: {$adminPool->available_funds} (available funds)\n";
echo "Cash on Hand currently shows: {$cashAccount->balance} (stored balance)\n";
echo "Difference: " . ($adminPool->available_funds - $cashAccount->balance) . "\n";

echo "\n=== RECOMMENDATION ===\n";
echo "Update Cash on Hand account balance to match admin pool available funds\n";
