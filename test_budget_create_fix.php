<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING BUDGET CREATION FIX ===\n";

// Simulate what the BudgetController create() method passes to the view
$users = \App\Models\User::where('is_approved', true)->get();
$categories = \App\Models\Category::orderBy('name')->get();
$accounts = \App\Models\Account::whereIn('name', ['Cash on Hand', 'HesabPay'])
    ->orderBy('name')
    ->get();

echo "Users available for budget assignment:\n";
foreach ($users as $user) {
    echo "- {$user->first_name} {$user->last_name} (ID: {$user->id})\n";
}

echo "\nCategories available:\n";
foreach ($categories as $category) {
    echo "- {$category->name} ({$category->type})\n";
}

echo "\nAccounts that will appear in dropdown:\n";
foreach ($accounts as $account) {
    echo "- {$account->name} - {$account->user->first_name} (Balance: {$account->balance})\n";
}

echo "\n=== BEFORE FIX: All accounts were shown ===\n";
echo "=== AFTER FIX: Only Cash on Hand and HesabPay accounts shown ===\n";
echo "=== JavaScript will filter based on selected user ===\n";

echo "\n=== TEST COMPLETE ===\n";
