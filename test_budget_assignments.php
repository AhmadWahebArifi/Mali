<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING BUDGET ASSIGNMENT SYSTEM ===\n";

// Get admin and test user
$admin = \App\Models\User::where('email', 'admin@mali.com')->first();
$ismail = \App\Models\User::where('email', 'ismail@mali.com')->first();

echo "Admin: {$admin->first_name} (ID: {$admin->id})\n";
echo "Test User: {$ismail->first_name} (ID: {$ismail->id})\n";

// Check if budget assignments exist
$assignments = \App\Models\BudgetAssignment::with(['budget', 'account', 'user'])
    ->where('user_id', $ismail->id)
    ->active()
    ->get();

echo "\n=== BUDGET ASSIGNMENTS FOR ISMAIL ===\n";
if ($assignments->isEmpty()) {
    echo "No budget assignments found for Ismail.\n";
} else {
    foreach ($assignments as $assignment) {
        echo "Assignment ID: {$assignment->id}\n";
        echo "Budget: {$assignment->budget->name}\n";
        echo "Account: {$assignment->account->name}\n";
        echo "Assigned Amount: {$assignment->assigned_amount}\n";
        echo "Remaining Amount: {$assignment->remaining_amount}\n";
        echo "Spent Amount: {$assignment->spent_amount}\n";
        echo "Assigned At: {$assignment->assigned_at}\n";
        echo "Status: {$assignment->status}\n";
        echo "---\n";
    }
}

// Test creating a new budget assignment
echo "\n=== CREATING NEW BUDGET ASSIGNMENT ===\n";

// Get Ismail's Cash on Hand account
$ismailAccount = \App\Models\Account::where('user_id', $ismail->id)
    ->where('name', 'Cash on Hand')
    ->first();

if (!$ismailAccount) {
    echo "Creating Cash on Hand account for Ismail...\n";
    $ismailAccount = \App\Models\Account::create([
        'user_id' => $ismail->id,
        'name' => 'Cash on Hand',
        'balance' => 0
    ]);
}

// Get a category for the budget
$category = \App\Models\Category::first();

echo "Creating budget for Ismail...\n";
$budget = \App\Models\Budget::create([
    'user_id' => $ismail->id,
    'category_id' => $category->id,
    'account_id' => $ismailAccount->id,
    'name' => 'Test Budget Assignment',
    'amount' => 500.00,
    'period' => 'monthly',
    'start_date' => date('Y-m-01'),
    'end_date' => date('Y-m-t'),
    'description' => 'Test budget assignment system'
]);

echo "Creating budget assignment record...\n";
$assignment = \App\Models\BudgetAssignment::create([
    'user_id' => $ismail->id,
    'budget_id' => $budget->id,
    'account_id' => $ismailAccount->id,
    'assigned_amount' => 500.00,
    'remaining_amount' => 500.00,
    'assignment_notes' => 'Test budget assignment',
    'assigned_at' => now(),
    'status' => 'active',
]);

echo "Budget Assignment Created:\n";
echo "- Budget: {$budget->name}\n";
echo "- User: {$assignment->user->first_name}\n";
echo "- Account: {$assignment->account->name}\n";
echo "- Amount: {$assignment->assigned_amount}\n";
echo "- Remaining: {$assignment->remaining_amount}\n";

echo "\n=== TEST COMPLETE ===\n";
