<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Approve/Reject Routes ===\n\n";

// Get a pending user to test with
$pendingUser = User::where('is_approved', false)->first();
if (!$pendingUser) {
    echo "No pending users found to test with.\n";
    exit(0);
}

echo "Testing with user: {$pendingUser->email} (ID: {$pendingUser->id})\n";

// Simulate admin session
\Illuminate\Support\Facades\Auth::login(User::where('email', 'admin@mali.com')->first());

// Test approve endpoint
$controller = new \App\Http\Controllers\Admin\UserManagementController();

echo "\n=== Testing Approve ===\n";
try {
    $response = $controller->approve($pendingUser);
    echo "Approve Response Status: " . $response->getStatusCode() . "\n";
    echo "Approve Response: " . $response->getContent() . "\n";
} catch (Exception $e) {
    echo "Approve Error: " . $e->getMessage() . "\n";
}

// Check if user is now approved
$userAfterApprove = User::find($pendingUser->id);
echo "User is_approved after approve: " . var_export($userAfterApprove->is_approved, true) . "\n";

// Create another test user for reject test
$newTestUser = User::create([
    'first_name' => 'Reject',
    'last_name' => 'Test',
    'email' => 'rejecttest@example.com',
    'password' => \Illuminate\Support\Facades\Hash::make('password123'),
    'is_approved' => false,
]);

echo "\n=== Testing Reject ===\n";
echo "Created test user for rejection: {$newTestUser->email} (ID: {$newTestUser->id})\n";

try {
    $response = $controller->reject($newTestUser);
    echo "Reject Response Status: " . $response->getStatusCode() . "\n";
    echo "Reject Response: " . $response->getContent() . "\n";
} catch (Exception $e) {
    echo "Reject Error: " . $e->getMessage() . "\n";
}

// Check if user was deleted
$userAfterReject = User::find($newTestUser->id);
echo "User exists after reject: " . ($userAfterReject ? "Yes" : "No") . "\n";
