<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;

echo "=== Testing Direct Controller Method ===\n\n";

// Get admin user and authenticate
$admin = User::where('email', 'admin@mali.com')->first();
auth()->login($admin);

// Create a test user
$testUser = User::create([
    'first_name' => 'Direct',
    'last_name' => 'Test',
    'email' => 'directtest' . time() . '@example.com',
    'password' => bcrypt('password'),
    'is_approved' => false,
]);

echo "✅ Created test user: " . $testUser->email . " (ID: " . $testUser->id . ")\n";

// Create a proper AJAX request
$request = new Request();
$request->headers->set('X-Requested-With', 'XMLHttpRequest');
$request->headers->set('Accept', 'application/json');
$request->headers->set('Content-Type', 'application/json');

// Set the request globally
app()->instance('request', $request);

// Test the controller method directly
$controller = new \App\Http\Controllers\Admin\UserManagementController();

echo "\n=== Testing Direct Controller Call ===\n";

try {
    $response = $controller->approve($request, $testUser->id);
    echo "✅ Controller method executed\n";
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n";
    
    $responseData = json_decode($response->getContent(), true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ Valid JSON response\n";
        echo "   Success: " . ($responseData['success'] ? 'true' : 'false') . "\n";
        echo "   Message: " . $responseData['message'] . "\n";
        echo "   User ID: " . $responseData['user_id'] . "\n";
    } else {
        echo "❌ Invalid JSON response: " . json_last_error_msg() . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Controller method failed: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

// Check if user was actually approved
$testUser->refresh();
if ($testUser->is_approved) {
    echo "✅ User was successfully approved\n";
} else {
    echo "❌ User was not approved\n";
}

echo "\n=== Debug Request Headers ===\n";
echo "X-Requested-With: " . $request->header('X-Requested-With') . "\n";
echo "Accept: " . $request->header('Accept') . "\n";
echo "Content-Type: " . $request->header('Content-Type') . "\n";
echo "expectsJson(): " . ($request->expectsJson() ? 'true' : 'false') . "\n";
echo "ajax(): " . ($request->ajax() ? 'true' : 'false') . "\n";

echo "\n=== Direct Controller Test Complete ===\n";
