<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING DEBUG VIEW OUTPUT ===\n";

// Test with regular user
$ismail = \App\Models\User::where('email', 'ismail@mali.com')->first();
Auth::login($ismail);

// Simulate the TransactionController create method
$controller = new \App\Http\Controllers\TransactionController();
$response = $controller->create();

// Get the view data
$viewData = $response->getData();
$accounts = $viewData['accounts'];

echo "User: " . Auth::user()->first_name . " (ID: " . Auth::id() . ")\n";
echo "Accounts count: " . $accounts->count() . "\n";

if ($accounts->count() > 0) {
    echo "First account: " . $accounts->first()->name . "\n";
    echo "All accounts:\n";
    foreach ($accounts as $account) {
        echo "  - " . $account->name . " (ID: " . $account->id . ")\n";
    }
} else {
    echo "❌ NO ACCOUNTS FOUND!\n";
}

echo "\n=== DEBUG INFO THAT WILL SHOW IN VIEW ===\n";
echo "DEBUG: Accounts count: " . $accounts->count() . " | ";
echo "User: " . Auth::user()->first_name . " (ID: " . Auth::id() . ")";
if ($accounts->count() > 0) {
    echo " | First account: " . $accounts->first()->name;
}
echo "\n";

echo "\n=== INSTRUCTIONS ===\n";
echo "1. Go to /transactions/create as a regular user\n";
echo "2. Look for the red debug text above the account dropdown\n";
echo "3. This will show exactly what accounts are being passed to the view\n";
echo "4. If it shows 0 accounts, the issue is in the controller\n";
echo "5. If it shows >0 accounts but dropdown is empty, the issue is in the view\n";
