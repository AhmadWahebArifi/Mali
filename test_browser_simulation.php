<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== Browser Simulation Test ===\n\n";

// Create a test user
$testUser = User::create([
    'first_name' => 'Browser',
    'last_name' => 'Test',
    'email' => 'browsertest' . time() . '@example.com',
    'password' => bcrypt('password'),
    'is_approved' => false,
]);

echo "✅ Created test user: " . $testUser->email . " (ID: " . $testUser->id . ")\n";

// Get admin user and authenticate
$admin = User::where('email', 'admin@mali.com')->first();
auth()->login($admin);

echo "\n=== Testing with cURL (like browser) ===\n";

// Initialize cURL session
$ch = curl_init();

// Set the URL
$url = "http://127.0.0.1:8000/admin/users/{$testUser->id}/approve";
curl_setopt($ch, CURLOPT_URL, $url);

// Set the request method
curl_setopt($ch, CURLOPT_POST, true);

// Set headers exactly like the browser
$headers = [
    'X-CSRF-TOKEN: ' . csrf_token(),
    'Accept: application/json',
    'Content-Type: application/json',
    'X-Requested-With: XMLHttpRequest',
    'Cookie: laravel_session=' . session()->getId()
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Return response instead of printing
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Follow redirects
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

// Execute the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ cURL Error: " . $error . "\n";
} else {
    echo "✅ cURL request executed\n";
    echo "HTTP Status: " . $httpCode . "\n";
    echo "Response: " . $response . "\n";
    
    $responseData = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ Valid JSON response\n";
        echo "   Success: " . ($responseData['success'] ? 'true' : 'false') . "\n";
        echo "   Message: " . $responseData['message'] . "\n";
        echo "   User ID: " . $responseData['user_id'] . "\n";
    } else {
        echo "❌ Invalid JSON response: " . json_last_error_msg() . "\n";
        echo "   Raw response: " . substr($response, 0, 200) . "...\n";
    }
}

// Check if user was approved
$testUser->refresh();
if ($testUser->is_approved) {
    echo "✅ User was successfully approved\n";
} else {
    echo "❌ User was not approved\n";
}

echo "\n=== Browser Simulation Complete ===\n";
echo "If this test shows the same issue, the problem is in the server-side route handling.\n";
echo "If this test passes, the issue is in the browser JavaScript.\n";
echo "\nTo debug the browser issue:\n";
echo "1. Open browser developer tools (F12)\n";
echo "2. Go to Network tab\n";
echo "3. Click Approve/Reject button\n";
echo "4. Check the actual request being sent\n";
echo "5. Check the response being received\n";
echo "6. Check Console tab for JavaScript errors\n";
