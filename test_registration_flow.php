<?php

require_once 'vendor/autoload.php';

use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Registration Controller Flow ===\n\n";

// Simulate registration request data
$requestData = [
    'first_name' => 'Test',
    'last_name' => 'Registration',
    'email' => 'testreg@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123'
];

// Create a request object
$request = Request::create('/register', 'POST', $requestData);
$request->headers->set('Content-Type', 'application/x-www-form-urlencoded');

// Create controller instance
$controller = new RegisteredUserController();

try {
    // Call the store method
    $response = $controller->store($request);
    
    echo "Registration completed successfully!\n";
    echo "Redirect to: " . $response->getTargetUrl() . "\n";
    
    // Check if user was created
    $user = \App\Models\User::where('email', 'testreg@example.com')->first();
    if ($user) {
        echo "User created with ID: {$user->id}\n";
        echo "is_approved: " . var_export($user->is_approved, true) . "\n";
        echo "is_approved (raw): " . var_export($user->getRawOriginal('is_approved'), true) . "\n";
    } else {
        echo "ERROR: User was not created!\n";
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Current Pending Users ===\n";
$pendingUsers = \App\Models\User::where('is_approved', false)->get();
echo "Pending count: " . $pendingUsers->count() . "\n";

foreach ($pendingUsers as $user) {
    echo "- {$user->email} (ID: {$user->id})\n";
}
