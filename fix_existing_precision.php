<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== FIXING EXISTING PRECISION ISSUES ===\n";

// Fix AdminBudgetPool precision
echo "1. Fixing AdminBudgetPool precision...\n";
$pool = \App\Models\AdminBudgetPool::getCurrent();
echo "Before fix - total_budget: " . $pool->total_budget . "\n";
echo "Before fix - total_allocated: " . $pool->total_allocated . "\n";

$pool->total_budget = round($pool->total_budget, 2);
$pool->total_allocated = round($pool->total_allocated, 2);
$pool->save();

echo "After fix - total_budget: " . $pool->fresh()->total_budget . "\n";
echo "After fix - total_allocated: " . $pool->fresh()->total_allocated . "\n";
echo "After fix - available_funds: " . $pool->fresh()->available_funds . "\n";

// Fix budget amounts precision
echo "\n2. Fixing budget amounts precision...\n";
$budgets = \App\Models\Budget::all();
foreach ($budgets as $budget) {
    echo "Budget ID {$budget->id} - Before: {$budget->amount} → ";
    $budget->amount = round($budget->amount, 2);
    $budget->save();
    echo "After: {$budget->amount}\n";
}

// Fix account balances precision
echo "\n3. Fixing account balances precision...\n";
$accounts = \App\Models\Account::all();
foreach ($accounts as $account) {
    echo "Account ID {$account->id} - Before: {$account->balance} → ";
    $account->balance = round($account->balance, 2);
    $account->save();
    echo "After: {$account->balance}\n";
}

echo "\n=== PRECISION FIXES COMPLETE ===\n";
