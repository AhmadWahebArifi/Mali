<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing User Approval System ===\n\n";

// Check if is_approved column exists
$columns = DB::getSchemaBuilder()->getColumnListing('users');
echo "Columns in users table: " . implode(', ', $columns) . "\n";
echo "Has is_approved column: " . (in_array('is_approved', $columns) ? 'YES' : 'NO') . "\n\n";

// Get all users
$users = User::all();
echo "Total users: " . $users->count() . "\n\n";

foreach ($users as $user) {
    echo "User ID: {$user->id}\n";
    echo "Email: {$user->email}\n";
    echo "First Name: {$user->first_name}\n";
    echo "Last Name: {$user->last_name}\n";
    echo "is_approved (raw): " . var_export($user->getRawOriginal('is_approved'), true) . "\n";
    echo "is_approved (casted): " . var_export($user->is_approved, true) . "\n";
    echo "---\n";
}

echo "\n=== Query Tests ===\n";
$pending = User::where('is_approved', false)->get();
echo "Pending users (is_approved = false): " . $pending->count() . "\n";

$approved = User::where('is_approved', true)->get();
echo "Approved users (is_approved = true): " . $approved->count() . "\n";

$nullApproved = User::whereNull('is_approved')->get();
echo "Users with null is_approved: " . $nullApproved->count() . "\n";
