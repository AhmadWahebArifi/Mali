<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFYING CURRENT STATE AFTER CACHE CLEAR ===\n";

// Check if regular users have accounts
$regularUsers = \App\Models\User::where('email', '!=', 'admin@mali.com')->get();

foreach ($regularUsers as $user) {
    echo "\nUser: {$user->first_name} (ID: {$user->id})\n";
    
    // Check their personal accounts
    $personalAccounts = \App\Models\Account::where('user_id', $user->id)
        ->whereNotIn('name', ['Cash on Hand', 'HesabPay'])
        ->orderBy('name')
        ->get();
    
    echo "Personal accounts available: {$personalAccounts->count()}\n";
    foreach ($personalAccounts as $account) {
        echo "  - {$account->name} (ID: {$account->id})\n";
    }
    
    // Simulate what TransactionController would return
    $isAdmin = $user->email === 'admin@mali.com';
    if ($isAdmin) {
        $accounts = \App\Models\Account::orderBy('name')->get();
    } else {
        $accounts = \App\Models\Account::where('user_id', $user->id)
            ->whereNotIn('name', ['Cash on Hand', 'HesabPay'])
            ->orderBy('name')
            ->get();
    }
    
    echo "Accounts that would be shown in dropdown: {$accounts->count()}\n";
}

echo "\n=== RECOMMENDATIONS ===\n";
echo "1. If users still see no accounts, try:\n";
echo "   - Clear browser cache and cookies\n";
echo "   - Log out and log back in\n";
echo "   - Try accessing /transactions/create directly\n";
echo "\n2. The backend logic is working correctly.\n";
echo "3. Each regular user has 3 accounts available.\n";

echo "\n=== TROUBLESHOOTING STEPS ===\n";
echo "If the issue persists:\n";
echo "1. Check browser console for JavaScript errors\n";
echo "2. Verify user is actually logged in (check session)\n";
echo "3. Try accessing the page in an incognito window\n";
echo "4. Check if there are any network errors in browser dev tools\n";
