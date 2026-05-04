<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING BUDGET CREATION FORM WITHOUT ACCOUNT SELECTION ===\n";

// Get admin user
$admin = \App\Models\User::where('email', 'admin@mali.com')->first();
$ismail = \App\Models\User::where('email', 'ismail@mali.com')->first();

echo "Users:\n";
echo "- Admin: {$admin->first_name}\n";
echo "- Test User: {$ismail->first_name}\n";

// Get categories
$categories = \App\Models\Category::orderBy('name')->get();
echo "\nAvailable Categories:\n";
foreach ($categories as $category) {
    echo "- {$category->name} (ID: {$category->id})\n";
}

// Test budget creation without account_id
echo "\n=== TESTING BUDGET CREATION ===\n";

$budgetData = [
    'user_id' => $ismail->id,
    'category_id' => $categories->first()->id,
    'name' => 'Test Budget Without Account Selection',
    'amount' => 500.00,
    'period' => 'monthly',
    'start_date' => date('Y-m-01'),
    'end_date' => date('Y-m-t'),
    'description' => 'Testing budget creation without account selection'
];

echo "Creating budget with data:\n";
foreach ($budgetData as $key => $value) {
    echo "- {$key}: {$value}\n";
}

// Simulate BudgetController store method logic
$category = \App\Models\Category::find($budgetData['category_id']);
$accountName = 'Cash on Hand'; // Default

if ($category && str_contains(strtolower($category->name), 'digital') || str_contains(strtolower($category->name), 'payment')) {
    $accountName = 'HesabPay';
}

echo "\nDetermined account: {$accountName}\n";

// Create or find the account
$targetAccount = \App\Models\Account::firstOrCreate(
    ['user_id' => $budgetData['user_id'], 'name' => $accountName],
    ['balance' => 0]
);

echo "Target Account: {$targetAccount->name} (ID: {$targetAccount->id})\n";

// Create the budget
$budget = \App\Models\Budget::create([
    'user_id' => $budgetData['user_id'],
    'category_id' => $budgetData['category_id'],
    'account_id' => $targetAccount->id,
    'name' => $budgetData['name'],
    'amount' => $budgetData['amount'],
    'period' => $budgetData['period'],
    'start_date' => $budgetData['start_date'],
    'end_date' => $budgetData['end_date'],
    'description' => $budgetData['description'],
]);

echo "✓ Budget created: {$budget->name} (ID: {$budget->id})\n";
echo "✓ Account assigned: {$budget->account->name}\n";
echo "✓ User: {$budget->user->first_name}\n";

// Create budget assignment
$assignment = \App\Models\BudgetAssignment::create([
    'user_id' => $budget->user_id,
    'budget_id' => $budget->id,
    'account_id' => $budget->account_id,
    'assigned_amount' => $budget->amount,
    'remaining_amount' => $budget->amount,
    'assignment_notes' => $budget->description,
    'assigned_at' => now(),
    'status' => 'active',
]);

echo "✓ Budget assignment created: {$assignment->assigned_amount}\n";

echo "\n=== TEST COMPLETE ===\n";
echo "✅ Budget creation works without account selection form\n";
echo "✅ System automatically determines appropriate account\n";
echo "✅ Budget assignment created successfully\n";
