<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING TRANSACTION PAGE ACCESS FOR USERS ===\n";

// Test regular user access
$ismail = \App\Models\User::where('email', 'ismail@mali.com')->first();
echo "Testing with user: {$ismail->first_name} ({$ismail->email})\n";

// Simulate authentication
\Auth::login($ismail);

// Test if user is authenticated
if (Auth::check()) {
    echo "✓ User is authenticated\n";
    echo "✓ User ID: " . Auth::id() . "\n";
    echo "✓ User Email: " . Auth::user()->email . "\n";
} else {
    echo "❌ User is NOT authenticated\n";
    exit;
}

// Test the TransactionController create method directly
try {
    $controller = new \App\Http\Controllers\TransactionController();
    
    echo "\n=== CALLING TRANSACTION CONTROLLER CREATE METHOD ===\n";
    
    // This should work if the user is authenticated
    $response = $controller->create();
    
    echo "✓ TransactionController::create() executed successfully\n";
    echo "Response type: " . get_class($response) . "\n";
    
    // Check if it's a view response
    if (method_exists($response, 'getData')) {
        $viewData = $response->getData();
        echo "View data keys: " . implode(', ', array_keys($viewData)) . "\n";
        
        if (isset($viewData['accounts'])) {
            $accounts = $viewData['accounts'];
            echo "Accounts passed to view: {$accounts->count()}\n";
            
            if ($accounts->count() > 0) {
                echo "Accounts list:\n";
                foreach ($accounts as $account) {
                    echo "  - {$account->name} (ID: {$account->id})\n";
                }
            } else {
                echo "❌ NO ACCOUNTS PASSED TO VIEW!\n";
            }
        } else {
            echo "❌ No accounts variable in view data!\n";
        }
        
        if (isset($viewData['categories'])) {
            $categories = $viewData['categories'];
            echo "Categories passed to view: {$categories->count()}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== MANUAL VERIFICATION ===\n";

// Manually verify the logic that should work
$user = Auth::user();
$isAdmin = $user->email === 'admin@mali.com';

echo "User: {$user->first_name}\n";
echo "Is Admin: " . ($isAdmin ? 'Yes' : 'No') . "\n";

if ($isAdmin) {
    $accounts = \App\Models\Account::orderBy('name')->get();
} else {
    // Regular users can only see their own accounts (excluding Cash on Hand and HesabPay)
    $accounts = \App\Models\Account::where('user_id', $user->id)
        ->whereNotIn('name', ['Cash on Hand', 'HesabPay'])
        ->orderBy('name')
        ->get();
}

echo "Manual calculation: {$accounts->count()} accounts\n";
foreach ($accounts as $account) {
    echo "  - {$account->name} (ID: {$account->id}, User ID: {$account->user_id})\n";
}

echo "\n=== TEST COMPLETE ===\n";
