<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CHECKING TRANSACTIONS TABLE STRUCTURE ===\n";

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Get column listing
$columns = Schema::getColumnListing('transactions');
echo "Columns in transactions table:\n";
foreach ($columns as $column) {
    echo "- {$column}\n";
}

// Check if budget_id column exists
if (in_array('budget_id', $columns)) {
    echo "\n✓ budget_id column exists\n";
    
    // Check column details
    $columnInfo = DB::select("DESCRIBE transactions `budget_id`");
    if (!empty($columnInfo)) {
        echo "Column details:\n";
        foreach ($columnInfo[0] as $key => $value) {
            echo "- {$key}: {$value}\n";
        }
    }
} else {
    echo "\n✗ budget_id column does NOT exist\n";
}

// Check if the migration was run
echo "\n=== CHECKING MIGRATIONS ===\n";
$migrationStatus = DB::select("SELECT migration FROM migrations WHERE migration LIKE '%budget_id%' ORDER BY id DESC LIMIT 5");
foreach ($migrationStatus as $migration) {
    echo "- {$migration->migration}\n";
}

echo "\n=== CHECK COMPLETE ===\n";
