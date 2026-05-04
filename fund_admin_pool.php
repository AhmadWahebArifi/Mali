<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== FUNDING ADMIN BUDGET POOL ===\n";

// Get or create admin budget pool
$adminPool = \App\Models\AdminBudgetPool::getCurrent();
echo "Current Admin Pool:\n";
echo "- Total Budget: {$adminPool->total_budget}\n";
echo "- Total Allocated: {$adminPool->total_allocated}\n";
echo "- Available Funds: {$adminPool->available_funds}\n";

// Add funds to admin pool
$amountToAdd = 5000.00;
$adminPool->addFunds($amountToAdd, "Initial funding for budget assignments");

echo "\nAfter adding {$amountToAdd}:\n";
echo "- Total Budget: {$adminPool->total_budget}\n";
echo "- Total Allocated: {$adminPool->total_allocated}\n";
echo "- Available Funds: {$adminPool->available_funds}\n";

echo "\n=== ADMIN POOL FUNDED ===\n";
