<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== FIXING DUPLICATE ACCOUNTS ===\n";

// Get all Cash on Hand and HesabPay accounts
$cashOnHandAccounts = \App\Models\Account::where('name', 'Cash on Hand')->get();
$hesabPayAccounts = \App\Models\Account::where('name', 'HesabPay')->get();

echo "Current Cash on Hand accounts:\n";
foreach ($cashOnHandAccounts as $account) {
    echo "- ID: {$account->id}, User: {$account->user->first_name}, Balance: {$account->balance}\n";
}

echo "\nCurrent HesabPay accounts:\n";
foreach ($hesabPayAccounts as $account) {
    echo "- ID: {$account->id}, User: {$account->user->first_name}, Balance: {$account->balance}\n";
}

// Fix Cash on Hand accounts
echo "\n=== FIXING CASH ON HAND ACCOUNTS ===\n";
$mainCashAccount = null;
$totalCashBalance = 0;

foreach ($cashOnHandAccounts as $account) {
    $totalCashBalance += $account->balance;
    
    if ($mainCashAccount === null) {
        // Keep the first one as the main account
        $mainCashAccount = $account;
        echo "Keeping main Cash on Hand account: ID {$account->id}\n";
    } else {
        // Move any budgets to the main account
        $budgetsCount = \App\Models\Budget::where('account_id', $account->id)->count();
        if ($budgetsCount > 0) {
            echo "Moving {$budgetsCount} budgets from account {$account->id} to main account {$mainCashAccount->id}\n";
            \App\Models\Budget::where('account_id', $account->id)->update(['account_id' => $mainCashAccount->id]);
        }
        
        // Delete the duplicate account
        echo "Deleting duplicate account: ID {$account->id}\n";
        $account->delete();
    }
}

// Update main account to be shared
if ($mainCashAccount) {
    $mainCashAccount->user_id = null;
    $mainCashAccount->balance = $totalCashBalance;
    $mainCashAccount->save();
    echo "Updated main Cash on Hand account to shared with total balance: {$totalCashBalance}\n";
}

// Fix HesabPay accounts
echo "\n=== FIXING HESABPAY ACCOUNTS ===\n";
$mainHesabAccount = null;
$totalHesabBalance = 0;

foreach ($hesabPayAccounts as $account) {
    $totalHesabBalance += $account->balance;
    
    if ($mainHesabAccount === null) {
        // Keep the first one as the main account
        $mainHesabAccount = $account;
        echo "Keeping main HesabPay account: ID {$account->id}\n";
    } else {
        // Move any budgets to the main account
        $budgetsCount = \App\Models\Budget::where('account_id', $account->id)->count();
        if ($budgetsCount > 0) {
            echo "Moving {$budgetsCount} budgets from account {$account->id} to main account {$mainHesabAccount->id}\n";
            \App\Models\Budget::where('account_id', $account->id)->update(['account_id' => $mainHesabAccount->id]);
        }
        
        // Delete the duplicate account
        echo "Deleting duplicate account: ID {$account->id}\n";
        $account->delete();
    }
}

// Update main account to be shared
if ($mainHesabAccount) {
    $mainHesabAccount->user_id = null;
    $mainHesabAccount->balance = $totalHesabBalance;
    $mainHesabAccount->save();
    echo "Updated main HesabPay account to shared with total balance: {$totalHesabBalance}\n";
}

echo "\n=== VERIFYING RESULTS ===\n";

$finalCashAccounts = \App\Models\Account::where('name', 'Cash on Hand')->get();
$finalHesabAccounts = \App\Models\Account::where('name', 'HesabPay')->get();

echo "Final Cash on Hand accounts: " . $finalCashAccounts->count() . "\n";
foreach ($finalCashAccounts as $account) {
    echo "- ID: {$account->id}, User: " . ($account->user ? $account->user->first_name : 'Shared') . ", Balance: {$account->balance}\n";
}

echo "Final HesabPay accounts: " . $finalHesabAccounts->count() . "\n";
foreach ($finalHesabAccounts as $account) {
    echo "- ID: {$account->id}, User: " . ($account->user ? $account->user->first_name : 'Shared') . ", Balance: {$account->balance}\n";
}

echo "\n=== FIX COMPLETE ===\n";
echo "✅ Duplicate accounts removed\n";
echo "✅ Main accounts converted to shared (user_id = null)\n";
echo "✅ Budgets moved to shared accounts\n";
