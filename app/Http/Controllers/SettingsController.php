<?php

namespace App\Http\Controllers;

use App\Helpers\FormatHelper;
use App\Models\UserSettings;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $settings = UserSettings::getForUser($user->id);
        
        return view('settings.index', compact('user', 'settings'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'currency' => 'required|string|in:' . implode(',', FormatHelper::getAvailableCurrencies()->pluck('code')->toArray()),
            'timezone' => 'required|string|in:' . implode(',', array_keys(FormatHelper::getAvailableTimezones())),
        ]);

        // Update user settings
        $settings = UserSettings::getForUser($user->id);
        $settings->update($validated);

        // Log the settings update
        LoggingService::logSettingsUpdate($user->id, $validated);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Settings saved successfully!',
                'currency' => $settings->currency,
                'timezone' => $settings->timezone,
            ]);
        }

        return redirect()->route('settings.index')
            ->with('success', 'Settings saved successfully!');
    }

    public function reset(Request $request)
    {
        $user = auth()->user();
        
        // Reset settings to defaults
        $settings = UserSettings::getForUser($user->id);
        $settings->update([
            'currency' => 'AFN',
            'timezone' => 'Asia/Kabul',
        ]);
        
        return redirect()->route('settings.index')
            ->with('success', 'Settings reset to defaults successfully!');
    }
}
