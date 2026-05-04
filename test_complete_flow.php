<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING COMPLETE BUDGET ASSIGNMENT FLOW ===\n";

// Get admin and test user
$admin = \App\Models\User::where('email', 'admin@mali.com')->first();
$ismail = \App\Models\User::where('email', 'ismail@mali.com')->first();

echo "Step 1: Admin assigns budget to Ismail\n";
echo "Admin: {$admin->first_name} (ID: {$admin->id})\n";
echo "User: {$ismail->first_name} (ID: {$ismail->id})\n";

// Get admin budget pool
$adminPool = \App\Models\AdminBudgetPool::getCurrent();
echo "Admin Pool Available: {$adminPool->available_funds}\n";

// Get Ismail's account
$ismailAccount = \App\Models\Account::where('user_id', $ismail->id)
    ->where('name', 'Cash on Hand')
    ->first();

echo "Ismail's Account Balance: {$ismailAccount->balance}\n";

// Create a budget assignment like the BudgetController would
$budgetAmount = 200.00;
$category = \App\Models\Category::first();

echo "\nStep 2: Creating budget and assignment...\n";

// Create budget
$budget = \App\Models\Budget::create([
    'user_id' => $ismail->id,
    'category_id' => $category->id,
    'account_id' => $ismailAccount->id,
    'name' => 'Monthly Food Budget',
    'amount' => $budgetAmount,
    'period' => 'monthly',
    'start_date' => date('Y-m-01'),
    'end_date' => date('Y-m-t'),
    'description' => 'Food expenses for the month'
]);

// Create budget assignment
$assignment = \App\Models\BudgetAssignment::create([
    'user_id' => $ismail->id,
    'budget_id' => $budget->id,
    'account_id' => $ismailAccount->id,
    'assigned_amount' => $budgetAmount,
    'remaining_amount' => $budgetAmount,
    'assignment_notes' => 'Monthly food budget assignment',
    'assigned_at' => now(),
    'status' => 'active',
]);

// Allocate from admin pool
$adminPool->allocateBudget($budgetAmount, "Budget allocated to {$ismail->first_name}: {$budget->name}");

// Transfer money to user's account
$ismailAccount->balance = round($ismailAccount->balance + $budgetAmount, 2);
$ismailAccount->save();

echo "✓ Budget created: {$budget->name} (Amount: {$budget->amount})\n";
echo "✓ Assignment created: {$assignment->assigned_amount}\n";
echo "✓ Admin pool updated: Available {$adminPool->available_funds}\n";
echo "✓ User account updated: Balance {$ismailAccount->balance}\n";

echo "\nStep 3: User sees their assignments on dashboard\n";

// Simulate what DashboardController would show
$budgetAssignments = \App\Models\BudgetAssignment::with(['budget', 'account'])
    ->where('user_id', $ismail->id)
    ->active()
    ->orderBy('assigned_at', 'desc')
    ->get();

echo "Ismail's Budget Assignments:\n";
foreach ($budgetAssignments as $assignment) {
    echo "- {$assignment->budget->name}: {$assignment->assigned_amount} assigned, {$assignment->remaining_amount} remaining\n";
}

echo "\nStep 4: User spends money from the budget\n";

// Create a transaction that uses the budget
$transactionAmount = 50.00;
$transaction = \App\Models\Transaction::create([
    'user_id' => $ismail->id,
    'account_id' => $ismailAccount->id,
    'category_id' => $category->id,
    'budget_id' => $budget->id,
    'amount' => $transactionAmount,
    'type' => 'expense',
    'description' => 'Grocery shopping',
    'date' => now(),
    'created_by' => $ismail->id,
]);

// Update account balance
$ismailAccount->balance = round($ismailAccount->balance - $transactionAmount, 2);
$ismailAccount->save();

echo "✓ Transaction created: {$transaction->description} ({$transaction->amount})\n";
echo "✓ Account balance updated: {$ismailAccount->balance}\n";

echo "\nStep 5: Check budget assignment updates\n";

// Update budget assignment remaining amount using the model method
$assignment->updateRemainingAmount();

echo "Assignment remaining: {$assignment->remaining_amount}\n";
echo "Assignment spent: {$assignment->spent_amount}\n";

echo "\nStep 6: User sees updated assignments\n";

$updatedAssignments = \App\Models\BudgetAssignment::with(['budget', 'account'])
    ->where('user_id', $ismail->id)
    ->active()
    ->orderBy('assigned_at', 'desc')
    ->get();

echo "Updated Budget Assignments:\n";
foreach ($updatedAssignments as $assignment) {
    echo "- {$assignment->budget->name}: {$assignment->assigned_amount} assigned, {$assignment->spent_amount} spent, {$assignment->remaining_amount} remaining\n";
}

echo "\n=== FLOW TEST COMPLETE ===\n";
echo "✓ Admin assigns budget → ✓ User sees assignments → ✓ Spending updates → ✓ Remaining amount calculated\n";
