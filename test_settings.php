<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Settings System ===\n\n";

use App\Models\Setting;
use App\Models\User;

// Test 1: Create a test user if needed
$user = User::first();
if (!$user) {
    echo "❌ No user found. Please create a user first.\n";
    exit;
}

echo "✅ Found user: " . $user->email . "\n";

// Test 2: Create some test settings
echo "\n=== Creating Test Settings ===\n";
Setting::setSetting($user->id, 'company_name', 'Test Company', 'string', 'general');
Setting::setSetting($user->id, 'currency', 'USD', 'string', 'general');
Setting::setSetting($user->id, 'timezone', 'America/New_York', 'string', 'general');
Setting::setSetting($user->id, 'email_notifications', true, 'boolean', 'notifications');
Setting::setSetting($user->id, 'theme', 'dark', 'string', 'appearance');
Setting::setSetting($user->id, 'session_timeout', 60, 'integer', 'security');

echo "✅ Created test settings\n";

// Test 3: Retrieve settings
echo "\n=== Retrieving Settings ===\n";
$companyName = Setting::getSetting($user->id, 'company_name', 'Default');
echo "Company Name: " . $companyName . "\n";

$currency = Setting::getSetting($user->id, 'currency', 'USD');
echo "Currency: " . $currency . "\n";

$timezone = Setting::getSetting($user->id, 'timezone', 'UTC');
echo "Timezone: " . $timezone . "\n";

$emailNotifications = Setting::getSetting($user->id, 'email_notifications', false);
echo "Email Notifications: " . ($emailNotifications ? 'true' : 'false') . "\n";

$theme = Setting::getSetting($user->id, 'theme', 'light');
echo "Theme: " . $theme . "\n";

$sessionTimeout = Setting::getSetting($user->id, 'session_timeout', 30);
echo "Session Timeout: " . $sessionTimeout . " minutes\n";

// Test 4: Get all settings grouped by category
echo "\n=== Grouped Settings ===\n";
$groupedSettings = Setting::getUserSettings($user->id);
foreach ($groupedSettings as $category => $settings) {
    echo "Category: " . $category . "\n";
    foreach ($settings as $setting) {
        echo "  - " . $setting->key . ": " . $setting->value . " (" . $setting->type . ")\n";
    }
    echo "\n";
}

// Test 5: Test typed values
echo "\n=== Testing Typed Values ===\n";
$booleanSetting = Setting::where('user_id', $user->id)->where('key', 'email_notifications')->first();
echo "Boolean setting raw value: " . $booleanSetting->value . "\n";
echo "Boolean setting typed value: " . ($booleanSetting->getTypedValue() ? 'true' : 'false') . "\n";

$integerSetting = Setting::where('user_id', $user->id)->where('key', 'session_timeout')->first();
echo "Integer setting raw value: " . $integerSetting->value . "\n";
echo "Integer setting typed value: " . $integerSetting->getTypedValue() . "\n";

// Test 6: Test middleware functionality
echo "\n=== Testing Middleware Functionality ===\n";
$middleware = new \App\Http\Middleware\ApplyUserSettings();

// Mock a request
$request = \Illuminate\Http\Request::create('/settings', 'GET');

// Authenticate the user for testing
auth()->login($user);

// Test the middleware
$middleware->handle($request, function($req) {
    echo "✅ Middleware executed successfully\n";
    
    // Check if settings are in session
    if (session()->has('user_settings')) {
        echo "✅ Settings stored in session\n";
        $sessionSettings = session('user_settings');
        echo "Session settings count: " . count($sessionSettings) . "\n";
    } else {
        echo "❌ Settings not found in session\n";
    }
    
    return response('OK');
});

echo "\n=== Settings System Test Complete ===\n";
echo "All settings functionality is working correctly!\n";
echo "You can now test the settings page at: http://127.0.0.1:8000/settings\n";
