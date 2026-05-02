<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Admin Users Section ===\n\n";

// Check if admin user exists
$admin = User::where('email', 'admin@mali.com')->first();
if (!$admin) {
    echo "ERROR: Admin user not found!\n";
    exit(1);
}

echo "Admin user found: {$admin->email}\n";

// Check pending users
$pendingUsers = User::where('is_approved', false)->get();
echo "\nPending users: " . $pendingUsers->count() . "\n";

foreach ($pendingUsers as $user) {
    echo "- ID: {$user->id}, Email: {$user->email}, Name: {$user->first_name} {$user->last_name}\n";
}

// Check approved users
$approvedUsers = User::where('is_approved', true)->get();
echo "\nApproved users: " . $approvedUsers->count() . "\n";

foreach ($approvedUsers as $user) {
    echo "- ID: {$user->id}, Email: {$user->email}, Name: {$user->first_name} {$user->last_name}\n";
}

// Test the JSON endpoint
echo "\n=== Testing JSON Endpoint ===\n";

// Simulate admin session
\Illuminate\Support\Facades\Auth::login($admin);

// Test the getUsersData endpoint
$controller = new \App\Http\Controllers\Admin\UserManagementController();
$response = $controller->getUsersData();

echo "JSON Response Status: " . $response->getStatusCode() . "\n";
echo "Response Data: " . $response->getContent() . "\n";
