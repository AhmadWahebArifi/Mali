<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Detailed Statement ===\n\n";

use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;

// Create a test request
$request = new Request([
    'start_date' => now()->subMonths(6)->format('Y-m-d'),
    'end_date' => now()->format('Y-m-d'),
]);

// Test the controller method
$controller = new ReportController();

try {
    echo "Testing detailedStatement method...\n";
    
    // Call the method
    $response = $controller->detailedStatement($request);
    
    echo "Response type: " . get_class($response) . "\n";
    
    if (method_exists($response, 'getData')) {
        $data = $response->getData();
        echo "View name: " . $response->getName() . "\n";
        echo "Data keys: " . implode(', ', array_keys($data)) . "\n";
        
        if (isset($data['transactions'])) {
            echo "Transactions count: " . $data['transactions']->count() . "\n";
        }
        if (isset($data['totalIncome'])) {
            echo "Total Income: $" . number_format($data['totalIncome'], 2) . "\n";
        }
        if (isset($data['totalExpenses'])) {
            echo "Total Expenses: $" . number_format($data['totalExpenses'], 2) . "\n";
        }
        if (isset($data['netCashFlow'])) {
            echo "Net Cash Flow: $" . number_format($data['netCashFlow'], 2) . "\n";
        }
    }
    
    echo "✅ Controller method works correctly\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

// Test if there are transactions
echo "\n=== Checking Transaction Data ===\n";
$transactionCount = \App\Models\Transaction::count();
echo "Total transactions in database: " . $transactionCount . "\n";

if ($transactionCount > 0) {
    $latestTransaction = \App\Models\Transaction::latest()->first();
    echo "Latest transaction: " . $latestTransaction->description . " (" . $latestTransaction->date->format('Y-m-d') . ")\n";
}

// Check categories and accounts
echo "\n=== Checking Related Data ===\n";
$categoryCount = \App\Models\Category::count();
$accountCount = \App\Models\Account::count();

echo "Categories: " . $categoryCount . "\n";
echo "Accounts: " . $accountCount . "\n";

if ($categoryCount > 0) {
    echo "Sample categories: " . \App\Models\Category::take(3)->pluck('name')->implode(', ') . "\n";
}

if ($accountCount > 0) {
    echo "Sample accounts: " . \App\Models\Account::take(3)->pluck('name')->implode(', ') . "\n";
}
