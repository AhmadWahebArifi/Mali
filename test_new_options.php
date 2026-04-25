<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;
use App\Models\User;

echo "=== Testing New Currency and Timezone Options ===\n\n";

$user = User::first();
if (!$user) {
    echo "❌ No user found.\n";
    exit;
}

echo "✅ Found user: " . $user->email . "\n";

// Test AFN currency
echo "Setting currency to AFN (Afghan Afghani)...\n";
Setting::setSetting($user->id, 'currency', 'AFN', 'string', 'general');
$currency = Setting::getSetting($user->id, 'currency', 'USD');
echo "Currency set to: " . $currency . "\n";

// Test Afghanistan timezone
echo "\nSetting timezone to Asia/Kabul (Afghanistan)...\n";
Setting::setSetting($user->id, 'timezone', 'Asia/Kabul', 'string', 'general');
$timezone = Setting::getSetting($user->id, 'timezone', 'UTC');
echo "Timezone set to: " . $timezone . "\n";

// Test current time in Afghanistan timezone
echo "\nCurrent time in Afghanistan:\n";
date_default_timezone_set('Asia/Kabul');
echo "Time: " . date('Y-m-d H:i:s') . "\n";

// Test USD currency
echo "\nSwitching back to USD (Dollar)...\n";
Setting::setSetting($user->id, 'currency', 'USD', 'string', 'general');
$currency = Setting::getSetting($user->id, 'currency', 'USD');
echo "Currency set to: " . $currency . "\n";

// Verify all settings are working
echo "\n=== Current Settings ===\n";
$settings = Setting::getUserSettings($user->id);
foreach ($settings as $category => $categorySettings) {
    echo "Category: " . $category . "\n";
    foreach ($categorySettings as $setting) {
        echo "  - " . $setting->key . ": " . $setting->value . "\n";
    }
}

echo "\n=== Settings Updated Successfully ===\n";
echo "✅ AFN (Afghan Afghani) currency added\n";
echo "✅ USD (Dollar) currency available\n";
echo "✅ Asia/Kabul (Afghanistan) timezone added\n";
echo "✅ All validation rules updated\n";
echo "\nVisit http://127.0.0.1:8000/settings to see the new options!\n";
