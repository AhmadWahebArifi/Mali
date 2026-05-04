<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== UPDATING CASH ON HAND TO SHOW AVAILABLE FUNDS ===\n";

function updateCashOnHandBalance() {
    // Get admin budget pool
    $adminPool = \App\Models\AdminBudgetPool::getCurrent();
    if (!$adminPool) {
        echo "❌ No admin budget pool found\n";
        return false;
    }

    // Get Cash on Hand account
    $cashAccount = \App\Models\Account::where('name', 'Cash on Hand')->first();
    if (!$cashAccount) {
        echo "❌ No Cash on Hand account found\n";
        return false;
    }

    $availableFunds = $adminPool->available_funds;
    $currentBalance = $cashAccount->balance;

    echo "Admin Pool Available Funds: {$availableFunds}\n";
    echo "Current Cash on Hand Balance: {$currentBalance}\n";

    // Update Cash on Hand account to match available funds
    $cashAccount->balance = $availableFunds;
    $cashAccount->save();

    echo "✅ Updated Cash on Hand balance to: {$availableFunds}\n";
    return true;
}

// Update the balance
if (updateCashOnHandBalance()) {
    echo "\n=== VERIFICATION ===\n";
    
    // Check updated state
    $adminPool = \App\Models\AdminBudgetPool::getCurrent();
    $cashAccount = \App\Models\Account::where('name', 'Cash on Hand')->first();
    
    echo "Admin Pool Available: {$adminPool->available_funds}\n";
    echo "Cash on Hand Balance: {$cashAccount->balance}\n";
    
    if ($adminPool->available_funds == $cashAccount->balance) {
        echo "✅ SUCCESS: Cash on Hand now shows available funds!\n";
    } else {
        echo "❌ FAILED: Values don't match\n";
    }
}

echo "\n=== UPDATE COMPLETE ===\n";
