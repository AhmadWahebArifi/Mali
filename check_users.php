<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== All Users in Database ===\n\n";

$allUsers = User::all();
echo "Total users: " . $allUsers->count() . "\n\n";

foreach ($allUsers as $user) {
    echo "User ID: {$user->id}\n";
    echo "Email: {$user->email}\n";
    echo "Name: {$user->first_name} {$user->last_name}\n";
    echo "is_approved: " . var_export($user->is_approved, true) . "\n";
    echo "is_approved (raw): " . var_export($user->getRawOriginal('is_approved'), true) . "\n";
    echo "Created at: {$user->created_at}\n";
    echo "---\n";
}

echo "\n=== Pending Users ===\n";
$pendingUsers = User::where('is_approved', false)->get();
echo "Pending count: " . $pendingUsers->count() . "\n";

foreach ($pendingUsers as $user) {
    echo "- {$user->email} (ID: {$user->id}, Name: {$user->first_name} {$user->last_name})\n";
}
