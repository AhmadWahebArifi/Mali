<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $settings = Setting::getUserSettings($user->id);
        
        // Define available settings with defaults
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
        
        // Get current values for each setting
        $currentSettings = [];
        foreach ($availableSettings as $category => $categorySettings) {
            foreach ($categorySettings as $key => $config) {
                $currentSettings[$key] = Setting::getSetting($user->id, $key, $config['default']);
            }
        }
        
        return view('settings.index', compact('user', 'availableSettings', 'currentSettings', 'settings'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'currency' => 'required|string|in:USD,AFN,EUR,GBP,JPY,CAD,AUD',
            'timezone' => 'required|string|in:UTC,Asia/Kabul,America/New_York,America/Los_Angeles,Europe/London,Asia/Tokyo',
            'date_format' => 'required|string|in:Y-m-d,m/d/Y,d/m/Y,F j, Y',
            'email_notifications' => 'boolean',
            'transaction_alerts' => 'boolean',
            'low_balance_alerts' => 'boolean',
            'monthly_reports' => 'boolean',
            'theme' => 'required|string|in:light,dark,auto',
            'two_factor_auth' => 'boolean',
            'session_timeout' => 'required|integer|min:5|max:1440',
            'login_notifications' => 'boolean',
        ]);

        // Update each setting
        $settingConfigs = [
            'company_name' => ['type' => 'string', 'category' => 'general'],
            'currency' => ['type' => 'string', 'category' => 'general'],
            'timezone' => ['type' => 'string', 'category' => 'general'],
            'date_format' => ['type' => 'string', 'category' => 'general'],
            'email_notifications' => ['type' => 'boolean', 'category' => 'notifications'],
            'transaction_alerts' => ['type' => 'boolean', 'category' => 'notifications'],
            'low_balance_alerts' => ['type' => 'boolean', 'category' => 'notifications'],
            'monthly_reports' => ['type' => 'boolean', 'category' => 'notifications'],
            'theme' => ['type' => 'string', 'category' => 'appearance'],
            'two_factor_auth' => ['type' => 'boolean', 'category' => 'security'],
            'session_timeout' => ['type' => 'integer', 'category' => 'security'],
            'login_notifications' => ['type' => 'boolean', 'category' => 'security'],
        ];

        foreach ($validated as $key => $value) {
            if (isset($settingConfigs[$key])) {
                $config = $settingConfigs[$key];
                Setting::setSetting(
                    $user->id,
                    $key,
                    $value,
                    $config['type'],
                    $config['category']
                );
            }
        }

        // Log the settings update
        LoggingService::logSettingsUpdate($user->id, $validated);

        // Apply settings throughout the project
        $this->applySettings($user->id);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Settings saved successfully!'
            ]);
        }

        return redirect()->route('settings.index')
            ->with('success', 'Settings saved successfully!');
    }

    private function applySettings($userId)
    {
        // Get all settings for the user
        $settings = Setting::where('user_id', $userId)->pluck('value', 'key')->toArray();
        
        // Apply timezone setting
        if (isset($settings['timezone'])) {
            config(['app.timezone' => $settings['timezone']]);
        }
        
        // Apply locale setting
        if (isset($settings['language'])) {
            app()->setLocale($settings['language']);
        }
        
        // Store settings in session for easy access
        session(['user_settings' => $settings]);
    }

    public function reset(Request $request)
    {
        $user = auth()->user();
        
        // Reset all settings to defaults
        Setting::where('user_id', $user->id)->delete();
        
        // Clear session settings
        session()->forget('user_settings');
        
        return redirect()->route('settings.index')
            ->with('success', 'Settings reset to defaults successfully!');
    }
}
