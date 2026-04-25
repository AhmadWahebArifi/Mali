<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing JSON Requests with X-Requested-With ===\n\n";

// Create a test user
$testUser = User::create([
    'first_name' => 'JSON',
    'last_name' => 'Test',
    'email' => 'jsontest@example.com',
    'password' => \Illuminate\Support\Facades\Hash::make('password123'),
    'is_approved' => false,
]);

echo "Created test user: {$testUser->email} (ID: {$testUser->id})\n";

// Simulate admin session
\Illuminate\Support\Facades\Auth::login(User::where('email', 'admin@mali.com')->first());

// Create a mock request with X-Requested-With header
$request = new \Illuminate\Http\Request();
$request->headers->set('X-Requested-With', 'XMLHttpRequest');
$request->headers->set('Accept', 'application/json');

// Set the request instance globally
app()->instance('request', $request);

echo "\n=== Testing Approve with JSON Request ===\n";
$controller = new \App\Http\Controllers\Admin\UserManagementController();

try {
    $response = $controller->approve($testUser);
    echo "Approve Response Status: " . $response->getStatusCode() . "\n";
    echo "Approve Response Content: " . $response->getContent() . "\n";
    
    if ($response->getStatusCode() === 200) {
        $data = json_decode($response->getContent(), true);
        echo "JSON Success: " . ($data['success'] ? 'Yes' : 'No') . "\n";
        echo "Message: " . ($data['message'] ?? 'No message') . "\n";
    }
} catch (Exception $e) {
    echo "Approve Error: " . $e->getMessage() . "\n";
}

// Check if user was approved
$userAfter = User::find($testUser->id);
echo "User is_approved after approve: " . var_export($userAfter->is_approved, true) . "\n";
