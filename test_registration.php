<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing User Registration ===\n\n";

// Create a test user
$user = User::create([
    'first_name' => 'Test',
    'last_name' => 'User2',
    'email' => 'test2@example.com',
    'password' => Hash::make('password123'),
    'is_approved' => false, // This user needs approval
]);

echo "Created test user:\n";
echo "ID: {$user->id}\n";
echo "Email: {$user->email}\n";
echo "is_approved: " . var_export($user->is_approved, true) . "\n";
echo "is_approved (raw): " . var_export($user->getRawOriginal('is_approved'), true) . "\n";

// Check pending users
$pending = User::where('is_approved', false)->get();
echo "\nPending users count: " . $pending->count() . "\n";

foreach ($pending as $pendingUser) {
    echo "- {$pendingUser->email} (ID: {$pendingUser->id})\n";
}
