<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== EXPENSE TRANSACTIONS ===\n";
$transactions = \App\Models\Transaction::with(['category', 'account'])
    ->where('type', 'expense')
    ->get();

foreach ($transactions as $t) {
    echo "ID: {$t->id}, Amount: {$t->amount}, Category ID: {$t->category_id} (" . ($t->category ? $t->category->name : 'null') . "), Account ID: {$t->account_id} (" . ($t->account ? $t->account->name : 'null') . "), Date: {$t->date}, Created By: {$t->created_by}, Account User ID: " . ($t->account ? $t->account->user_id : 'null') . "\n";
}

echo "\n=== BUDGET DETAILS ===\n";
$budgets = \App\Models\Budget::with(['category', 'account'])
    ->where('is_active', true)
    ->get();

foreach ($budgets as $b) {
    echo "Budget ID: {$b->id}, Name: {$b->name}, User ID: {$b->user_id}, Category ID: {$b->category_id} (" . ($b->category ? $b->category->name : 'null') . "), Account ID: {$b->account_id} (" . ($b->account ? $b->account->name : 'null') . "), Period: {$b->period}, Amount: {$b->amount}\n";
}

echo "\n=== MANUAL SQL TEST ===\n";
$budget = $budgets->first();
if ($budget) {
    $sql = "SELECT COALESCE(SUM(t.amount), 0) as total 
            FROM transactions t 
            INNER JOIN accounts a ON t.account_id = a.id 
            WHERE a.user_id = ? 
            AND t.type = 'expense' 
            AND (t.category_id IS NULL OR t.category_id = ?) 
            AND (t.account_id IS NULL OR t.account_id = ?) 
            AND MONTH(t.date) = ? 
            AND YEAR(t.date) = ?";
    
    $result = \Illuminate\Support\Facades\DB::select($sql, [
        $budget->user_id, 
        $budget->category_id, 
        $budget->account_id, 
        date('m'), 
        date('Y')
    ]);
    
    echo "SQL Result: " . $result[0]->total . "\n";
    echo "Expected: Should match Food transaction amount if categories/accounts align\n";
}
