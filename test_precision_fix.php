<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING PRECISION FIXES ===\n";

// Create a fresh admin pool for testing
echo "1. Creating fresh admin pool...\n";
$pool = new \App\Models\AdminBudgetPool();
$pool->total_budget = 0;
$pool->total_allocated = 0;
$pool->description = 'Test pool';
$pool->save();

echo "Initial total_budget: " . $pool->total_budget . "\n";
echo "Initial total_allocated: " . $pool->total_allocated . "\n";
echo "Initial available_funds: " . $pool->available_funds . "\n";

// Test adding 1000
echo "\n2. Adding 1000 to pool...\n";
$pool->addFunds(1000);
echo "After adding 1000 - total_budget: " . $pool->fresh()->total_budget . "\n";
echo "After adding 1000 - available_funds: " . $pool->fresh()->available_funds . "\n";

// Test allocating 1000
echo "\n3. Allocating 1000 from pool...\n";
$pool->allocateBudget(1000);
echo "After allocating 1000 - total_allocated: " . $pool->fresh()->total_allocated . "\n";
echo "After allocating 1000 - available_funds: " . $pool->fresh()->available_funds . "\n";

// Test returning 1000
echo "\n4. Returning 1000 to pool...\n";
$pool->returnBudget(1000);
echo "After returning 1000 - total_allocated: " . $pool->fresh()->total_allocated . "\n";
echo "After returning 1000 - available_funds: " . $pool->fresh()->available_funds . "\n";

// Clean up
$pool->delete();

echo "\n=== PRECISION FIX TEST COMPLETE ===\n";
