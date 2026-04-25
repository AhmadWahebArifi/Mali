<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== Testing Frontend AJAX Requests ===\n\n";

// Create a test pending user
$testUser = User::create([
    'first_name' => 'Frontend',
    'last_name' => 'Test',
    'email' => 'frontendtest' . time() . '@example.com',
    'password' => bcrypt('password'),
    'is_approved' => false,
]);

echo "✅ Created test user: " . $testUser->email . " (ID: " . $testUser->id . ")\n";

// Simulate the exact AJAX request that the frontend would make
echo "\n=== Simulating Frontend AJAX Request ===\n";

// Get admin user and authenticate
$admin = User::where('email', 'admin@mali.com')->first();
auth()->login($admin);

// Create request with all headers that the frontend sends
$request = \Illuminate\Http\Request::create(
    "/admin/users/{$testUser->id}/approve",
    'POST',
    [], // POST data
    [], // FILES
    [], // COOKIE
    [], // FILES
    [
        'HTTP_X-CSRF_TOKEN' => csrf_token(),
        'HTTP_ACCEPT' => 'application/json',
        'HTTP_CONTENT_TYPE' => 'application/json',
        'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
    ]
);

// Set the request globally
app()->instance('request', $request);

// Test the route directly
try {
    $route = Route::getRoutes()->match($request);
    $response = $route->run();
    
    echo "✅ Route executed successfully\n";
    echo "Status Code: " . $response->getStatusCode() . "\n";
    echo "Response Headers: " . json_encode($response->headers->all()) . "\n";
    echo "Response Content: " . $response->getContent() . "\n";
    
    // Check if it's valid JSON
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
    echo "❌ Route execution failed: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

// Test the getUsersData endpoint as well
echo "\n=== Testing getUsersData Endpoint ===\n";

$dataRequest = \Illuminate\Http\Request::create(
    "/admin/users/data",
    'GET',
    [], // POST data
    [], // FILES
    [], // COOKIE
    [], // FILES
    [
        'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
        'HTTP_ACCEPT' => 'application/json'
    ]
);

app()->instance('request', $dataRequest);

try {
    $route = Route::getRoutes()->match($dataRequest);
    $response = $route->run();
    
    echo "✅ getUsersData route executed\n";
    echo "Status Code: " . $response->getStatusCode() . "\n";
    
    $responseData = json_decode($response->getContent(), true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ Valid JSON response\n";
        echo "   Pending Count: " . $responseData['pending_count'] . "\n";
        echo "   Approved Count: " . $responseData['approved_count'] . "\n";
        echo "   Pending Users: " . count($responseData['pending']) . "\n";
        echo "   Approved Users: " . count($responseData['approved']) . "\n";
    } else {
        echo "❌ Invalid JSON response: " . json_last_error_msg() . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ getUsersData execution failed: " . $e->getMessage() . "\n";
}

echo "\n=== Frontend Test Complete ===\n";
echo "If the above tests pass, the issue might be in the JavaScript execution in the browser.\n";
echo "Common issues:\n";
echo "1. JavaScript errors in browser console\n";
echo "2. CSRF token issues\n";
echo "3. Network connectivity issues\n";
echo "4. Browser caching issues\n";
echo "\nCheck the browser console (F12) for any JavaScript errors when clicking the buttons.\n";
