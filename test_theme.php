<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;
use App\Models\User;

echo "=== Testing Theme Settings ===\n\n";

$user = User::first();
if (!$user) {
    echo "❌ No user found.\n";
    exit;
}

echo "✅ Found user: " . $user->email . "\n";

// Check current theme
$currentTheme = Setting::getSetting($user->id, 'theme', 'light');
echo "Current theme: " . $currentTheme . "\n";

// Set theme to light to fix the dark sidebar issue
echo "Setting theme to light...\n";
Setting::setSetting($user->id, 'theme', 'light', 'string', 'appearance');

// Verify the change
$newTheme = Setting::getSetting($user->id, 'theme', 'light');
echo "New theme: " . $newTheme . "\n";

// Test middleware
echo "\n=== Testing Middleware ===\n";
$middleware = new \App\Http\Middleware\ApplyUserSettings();
$request = \Illuminate\Http\Request::create('/settings', 'GET');

auth()->login($user);

$middleware->handle($request, function($req) {
    echo "✅ Middleware applied\n";
    
    if (session()->has('user_settings')) {
        $settings = session('user_settings');
        echo "Theme in session: " . ($settings['theme'] ?? 'not set') . "\n";
    }
    
    return response('OK');
});

echo "\n=== Theme Fix Complete ===\n";
echo "The theme has been set to 'light' to fix the dark sidebar issue.\n";
echo "Visit http://127.0.0.1:8000/settings to see the corrected theme.\n";
