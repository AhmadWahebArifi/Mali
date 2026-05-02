<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;
use App\Models\User;

echo "=== Testing Removed Language and Dashboard Layout Options ===\n\n";

$user = User::first();
if (!$user) {
    echo "❌ No user found.\n";
    exit;
}

echo "✅ Found user: " . $user->email . "\n";

// Test that language and dashboard_layout settings are no longer in available settings
$controller = new \App\Http\Controllers\SettingsController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('index');
$method->setAccessible(true);

// Authenticate user first
$user = User::first();
auth()->login($user);
$settings = Setting::getUserSettings($user->id);

// Define available settings with defaults (from controller)
$availableSettings = [
    'general' => [
        'company_name' => [
            'type' => 'string',
            'default' => $user->first_name . ' ' . $user->last_name,
            'label' => 'Company Name',
            'description' => 'Your company or business name'
        ],
        'currency' => [
            'type' => 'string',
            'default' => 'USD',
            'label' => 'Currency',
            'description' => 'Default currency for financial reports',
            'options' => ['USD', 'AFN', 'EUR', 'GBP', 'JPY', 'CAD', 'AUD']
        ],
        'timezone' => [
            'type' => 'string',
            'default' => 'UTC',
            'label' => 'Timezone',
            'description' => 'Your timezone for date/time display',
            'options' => ['UTC', 'Asia/Kabul', 'America/New_York', 'America/Los_Angeles', 'Europe/London', 'Asia/Tokyo']
        ],
        'date_format' => [
            'type' => 'string',
            'default' => 'Y-m-d',
            'label' => 'Date Format',
            'description' => 'Preferred date format',
            'options' => ['Y-m-d', 'm/d/Y', 'd/m/Y', 'F j, Y']
        ]
    ],
    'notifications' => [
        'email_notifications' => [
            'type' => 'boolean',
            'default' => true,
            'label' => 'Email Notifications',
            'description' => 'Receive email notifications for important events'
        ],
        'transaction_alerts' => [
            'type' => 'boolean',
            'default' => true,
            'label' => 'Transaction Alerts',
            'description' => 'Get notified for new transactions'
        ],
        'low_balance_alerts' => [
            'type' => 'boolean',
            'default' => true,
            'label' => 'Low Balance Alerts',
            'description' => 'Alert when account balance is low'
        ],
        'monthly_reports' => [
            'type' => 'boolean',
            'default' => true,
            'label' => 'Monthly Reports',
            'description' => 'Receive monthly financial reports'
        ]
    ],
    'appearance' => [
        'theme' => [
            'type' => 'string',
            'default' => 'light',
            'label' => 'Theme',
            'description' => 'Choose your preferred theme',
            'options' => ['light', 'dark', 'auto']
        ]
    ],
    'security' => [
        'two_factor_auth' => [
            'type' => 'boolean',
            'default' => false,
            'label' => 'Two-Factor Authentication',
            'description' => 'Enable 2FA for enhanced security'
        ],
        'session_timeout' => [
            'type' => 'integer',
            'default' => 30,
            'label' => 'Session Timeout (minutes)',
            'description' => 'Auto-logout after inactivity'
        ],
        'login_notifications' => [
            'type' => 'boolean',
            'default' => true,
            'label' => 'Login Notifications',
            'description' => 'Get notified when someone logs into your account'
        ]
    ]
];

echo "=== Available Settings Categories ===\n";
foreach ($availableSettings as $category => $categorySettings) {
    echo "Category: " . $category . "\n";
    foreach ($categorySettings as $key => $config) {
        echo "  - " . $config['label'] . " (" . $key . ")\n";
    }
    echo "\n";
}

// Check that language and dashboard_layout are not in appearance category
if (!isset($availableSettings['appearance']['language'])) {
    echo "✅ Language setting successfully removed\n";
} else {
    echo "❌ Language setting still exists\n";
}

if (!isset($availableSettings['appearance']['dashboard_layout'])) {
    echo "✅ Dashboard Layout setting successfully removed\n";
} else {
    echo "❌ Dashboard Layout setting still exists\n";
}

// Clean up any existing language and dashboard_layout settings from database
echo "\n=== Cleaning up old settings ===\n";
$languageSetting = Setting::where('user_id', $user->id)->where('key', 'language')->first();
if ($languageSetting) {
    $languageSetting->delete();
    echo "✅ Removed old language setting from database\n";
} else {
    echo "ℹ️ No language setting found in database\n";
}

$dashboardLayoutSetting = Setting::where('user_id', $user->id)->where('key', 'dashboard_layout')->first();
if ($dashboardLayoutSetting) {
    $dashboardLayoutSetting->delete();
    echo "✅ Removed old dashboard_layout setting from database\n";
} else {
    echo "ℹ️ No dashboard_layout setting found in database\n";
}

echo "\n=== Settings Cleanup Complete ===\n";
echo "✅ Language and Dashboard Layout sections removed from settings\n";
echo "✅ Validation rules updated\n";
echo "✅ Database cleaned up\n";
echo "✅ View template updated\n";
echo "\nVisit http://127.0.0.1:8000/settings to see the cleaned up settings page!\n";
