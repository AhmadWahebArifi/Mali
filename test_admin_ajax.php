<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== Testing Admin AJAX Responses ===\n\n";

// Get admin user
$admin = User::where('email', 'admin@mali.com')->first();
auth()->login($admin);

// Create a test pending user
$testUser = User::create([
    'first_name' => 'AJAX',
    'last_name' => 'Test',
    'email' => 'ajaxtest' . time() . '@example.com',
    'password' => bcrypt('password'),
    'is_approved' => false,
]);

echo "✅ Created test user: " . $testUser->email . "\n";

// Test approve with AJAX request
echo "\n=== Testing Approve with AJAX ===\n";

// Create AJAX request
$request = new \Illuminate\Http\Request();
$request->headers->set('X-Requested-With', 'XMLHttpRequest');
$request->headers->set('Accept', 'application/json');

// Simulate the request being set globally
app()->instance('request', $request);

$controller = new \App\Http\Controllers\Admin\UserManagementController();

try {
    $response = $controller->approve($testUser);
    echo "✅ Approve method executed\n";
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n";
    
    $responseData = json_decode($response->getContent(), true);
    if ($responseData && isset($responseData['success'])) {
        echo "✅ JSON response returned correctly\n";
        echo "   Success: " . ($responseData['success'] ? 'true' : 'false') . "\n";
        echo "   Message: " . $responseData['message'] . "\n";
    } else {
        echo "❌ Not a JSON response\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Approve method failed: " . $e->getMessage() . "\n";
}

// Create another test user for reject
$rejectUser = User::create([
    'first_name' => 'Reject',
    'last_name' => 'AJAX',
    'email' => 'rejectajax' . time() . '@example.com',
    'password' => bcrypt('password'),
    'is_approved' => false,
]);

echo "\n=== Testing Reject with AJAX ===\n";

try {
    $response = $controller->reject($rejectUser);
    echo "✅ Reject method executed\n";
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n";
    
    $responseData = json_decode($response->getContent(), true);
    if ($responseData && isset($responseData['success'])) {
        echo "✅ JSON response returned correctly\n";
        echo "   Success: " . ($responseData['success'] ? 'true' : 'false') . "\n";
        echo "   Message: " . $responseData['message'] . "\n";
    } else {
        echo "❌ Not a JSON response\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Reject method failed: " . $e->getMessage() . "\n";
}

echo "\n=== AJAX Test Complete ===\n";
echo "The controller methods should now return proper JSON responses for AJAX requests.\n";
echo "The approve/reject functionality should work in the browser now.\n";
