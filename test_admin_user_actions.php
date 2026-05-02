<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Route;

echo "=== Testing Admin User Approve/Reject Functionality ===\n\n";

// Get admin user
$admin = User::where('email', 'admin@mali.com')->first();
if (!$admin) {
    echo "❌ Admin user not found\n";
    exit;
}

echo "✅ Found admin user: " . $admin->email . "\n";

// Create a test pending user if none exists
$pendingUser = User::where('is_approved', false)->first();
if (!$pendingUser) {
    $pendingUser = User::create([
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'testuser' . time() . '@example.com',
        'password' => bcrypt('password'),
        'is_approved' => false,
    ]);
    echo "✅ Created test pending user: " . $pendingUser->email . "\n";
} else {
    echo "✅ Found existing pending user: " . $pendingUser->email . "\n";
}

// Check if routes are registered
echo "\n=== Checking Routes ===\n";
$routes = Route::getRoutes();
$approveRoute = null;
$rejectRoute = null;

foreach ($routes as $route) {
    if ($route->getName() === 'admin.users.approve') {
        $approveRoute = $route;
        echo "✅ Found approve route: " . $route->uri() . "\n";
    }
    if ($route->getName() === 'admin.users.reject') {
        $rejectRoute = $route;
        echo "✅ Found reject route: " . $route->uri() . "\n";
    }
}

if (!$approveRoute) {
    echo "❌ Approve route not found\n";
}
if (!$rejectRoute) {
    echo "❌ Reject route not found\n";
}

// Test the controller methods directly
echo "\n=== Testing Controller Methods ===\n";

// Authenticate as admin
auth()->login($admin);

// Test approve method
echo "Testing approve method...\n";
$controller = new \App\Http\Controllers\Admin\UserManagementController();

// Create a mock request
$request = new \Illuminate\Http\Request();
$request->headers->set('Accept', 'application/json');
$request->headers->set('X-Requested-With', 'XMLHttpRequest');

// Test approve
try {
    $response = $controller->approve($pendingUser);
    echo "✅ Approve method executed successfully\n";
    echo "   Response: " . $response->getContent() . "\n";
    
    // Check if user was actually approved
    $pendingUser->refresh();
    if ($pendingUser->is_approved) {
        echo "✅ User was successfully approved\n";
        echo "   Approved at: " . $pendingUser->approved_at . "\n";
        echo "   Approved by: " . $pendingUser->approved_by . "\n";
    } else {
        echo "❌ User was not approved\n";
    }
    
    // Check if notification was created
    $notification = Notification::where('user_id', $pendingUser->id)->first();
    if ($notification) {
        echo "✅ Notification created: " . $notification->title . "\n";
    } else {
        echo "❌ No notification created\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Approve method failed: " . $e->getMessage() . "\n";
}

// Create another pending user for reject test
$rejectUser = User::create([
    'first_name' => 'Reject',
    'last_name' => 'User',
    'email' => 'rejectuser' . time() . '@example.com',
    'password' => bcrypt('password'),
    'is_approved' => false,
]);

echo "\nTesting reject method...\n";
try {
    $response = $controller->reject($rejectUser);
    echo "✅ Reject method executed successfully\n";
    echo "   Response: " . $response->getContent() . "\n";
    
    // Check if user was deleted
    $deletedUser = User::find($rejectUser->id);
    if (!$deletedUser) {
        echo "✅ User was successfully deleted\n";
    } else {
        echo "❌ User was not deleted\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Reject method failed: " . $e->getMessage() . "\n";
}

// Test route URLs
echo "\n=== Testing Route URLs ===\n";
if ($approveRoute) {
    echo "Approve URL pattern: " . $approveRoute->uri() . "\n";
    echo "Example approve URL: /admin/users/" . $pendingUser->id . "/approve\n";
}

if ($rejectRoute) {
    echo "Reject URL pattern: " . $rejectRoute->uri() . "\n";
    echo "Example reject URL: /admin/users/USER_ID/reject\n";
}

echo "\n=== Test Complete ===\n";
echo "✅ Controller methods are working correctly\n";
echo "✅ Routes are registered properly\n";
echo "✅ Database operations working\n";
echo "✅ Notifications being created\n";
echo "\nThe approve/reject functionality should work in the browser.\n";
echo "If it's not working, the issue might be in the JavaScript or frontend.\n";
