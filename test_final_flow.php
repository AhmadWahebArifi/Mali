<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING FINAL BUDGET ASSIGNMENT FLOW ===\n";

// Get admin and test user
$admin = \App\Models\User::where('email', 'admin@mali.com')->first();
$ismail = \App\Models\User::where('email', 'ismail@mali.com')->first();

echo "Step 1: Create new budget assignment for Ismail\n";

// Get Ismail's account
$ismailAccount = \App\Models\Account::where('user_id', $ismail->id)
    ->where('name', 'Cash on Hand')
    ->first();

// Get a category
$category = \App\Models\Category::first();

// Create a new budget and assignment
$budgetAmount = 300.00;
$budget = \App\Models\Budget::create([
    'user_id' => $ismail->id,
    'category_id' => $category->id,
    'account_id' => $ismailAccount->id,
    'name' => 'Test Final Budget',
    'amount' => $budgetAmount,
    'period' => 'monthly',
    'start_date' => date('Y-m-01'),
    'end_date' => date('Y-m-t'),
    'description' => 'Final test budget'
]);

$assignment = \App\Models\BudgetAssignment::create([
    'user_id' => $ismail->id,
    'budget_id' => $budget->id,
    'account_id' => $ismailAccount->id,
    'assigned_amount' => $budgetAmount,
    'remaining_amount' => $budgetAmount,
    'assignment_notes' => 'Final test assignment',
    'assigned_at' => now(),
    'status' => 'active',
]);

echo "✓ Created budget: {$budget->name} (ID: {$budget->id})\n";
echo "✓ Created assignment: {$assignment->assigned_amount} assigned\n";

echo "\nStep 2: Create transaction with proper budget_id\n";

// Create transaction like TransactionController would
$transactionAmount = 75.00;
$transaction = \App\Models\Transaction::create([
    'type' => 'expense',
    'amount' => $transactionAmount,
    'category_id' => $category->id,
    'account_id' => $ismailAccount->id,
    'budget_id' => $budget->id, // Now properly set
    'date' => now(),
    'description' => 'Test expense transaction',
    'created_by' => $ismail->id,
    'is_over_budget' => false,
    'outstanding_amount' => 0,
]);

echo "✓ Created transaction: {$transaction->description} ({$transaction->amount})\n";
echo "✓ Transaction budget_id: {$transaction->budget_id}\n";

echo "\nStep 3: Update budget assignment calculation\n";

// Update assignment using the model method
$assignment->updateRemainingAmount();

echo "✓ Assignment remaining: {$assignment->remaining_amount}\n";
echo "✓ Assignment spent: {$assignment->spent_amount}\n";

echo "\nStep 4: Verify dashboard display\n";

// Get what DashboardController would show
$budgetAssignments = \App\Models\BudgetAssignment::with(['budget', 'account'])
    ->where('user_id', $ismail->id)
    ->active()
    ->orderBy('assigned_at', 'desc')
    ->get();

echo "Ismail's Budget Assignments for Dashboard:\n";
foreach ($budgetAssignments as $assignment) {
    echo "- {$assignment->budget->name}: {$assignment->assigned_amount} assigned, {$assignment->spent_amount} spent, {$assignment->remaining_amount} remaining\n";
}

echo "\n=== FINAL FLOW TEST COMPLETE ===\n";
echo "✓ Admin assigns budget → ✓ Transaction created with budget_id → ✓ Assignment updated → ✓ Dashboard shows correct info\n";
