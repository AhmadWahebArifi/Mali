<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;

class ApplyUserSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply settings for authenticated users
        if (auth()->check()) {
            $this->applyUserSettings(auth()->id());
        }

        return $next($request);
    }

    /**
     * Apply user settings throughout the application
     */
    private function applyUserSettings($userId)
    {
        // Get all settings for the user
        $settings = Setting::where('user_id', $userId)->pluck('value', 'key')->toArray();
        
        // Store settings in session for easy access
        session(['user_settings' => $settings]);
        
        // Apply timezone setting
        if (isset($settings['timezone'])) {
            config(['app.timezone' => $settings['timezone']]);
            date_default_timezone_set($settings['timezone']);
        }
        
        // Apply locale setting
        if (isset($settings['language'])) {
            app()->setLocale($settings['language']);
        }
        
        // Apply currency setting (for views)
        if (isset($settings['currency'])) {
            config(['app.currency' => $settings['currency']]);
        }
        
        // Apply date format setting
        if (isset($settings['date_format'])) {
            config(['app.date_format' => $settings['date_format']]);
        }
        
        // Apply session timeout
        if (isset($settings['session_timeout'])) {
            config(['session.lifetime' => $settings['session_timeout']]);
        }
        
        // Apply theme setting
        if (isset($settings['theme'])) {
            config(['app.theme' => $settings['theme']]);
        }
        
        // Share settings with all views
        view()->share('userSettings', $settings);
    }
}
