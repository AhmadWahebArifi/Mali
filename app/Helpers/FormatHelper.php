<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;

class FormatHelper
{
    // Currency symbols mapping
    private static $currencySymbols = [
        'AFN' => '؋',
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'JPY' => '¥',
        'CNY' => '¥',
        'INR' => '₹',
        'PKR' => '₨',
        'IRR' => '﷼',
        'SAR' => '﷼',
        'AED' => 'د.إ',
        'CAD' => 'C$',
        'AUD' => 'A$',
    ];

    // Currency names mapping
    private static $currencyNames = [
        'AFN' => 'Afghan Afghani',
        'USD' => 'US Dollar',
        'EUR' => 'Euro',
        'GBP' => 'British Pound',
        'JPY' => 'Japanese Yen',
        'CNY' => 'Chinese Yuan',
        'INR' => 'Indian Rupee',
        'PKR' => 'Pakistani Rupee',
        'IRR' => 'Iranian Rial',
        'SAR' => 'Saudi Riyal',
        'AED' => 'UAE Dirham',
        'CAD' => 'Canadian Dollar',
        'AUD' => 'Australian Dollar',
    ];

    /**
     * Format amount with user's currency
     */
    public static function currency($amount, $currency = null, $showSymbol = true)
    {
        $currency = $currency ?? self::getUserCurrency();
        $symbol = self::$currencySymbols[$currency] ?? $currency;
        $name = self::$currencyNames[$currency] ?? $currency;
        
        $formattedAmount = number_format($amount, 2);
        
        if ($showSymbol) {
            return "{$symbol}{$formattedAmount}";
        }
        
        return $formattedAmount . ' ' . $currency;
    }

    /**
     * Get user's currency from settings
     */
    private static function getUserCurrency()
    {
        if (auth()->check()) {
            return auth()->user()->currency;
        }
        return 'AFN'; // Default
    }

    /**
     * Format date with user's timezone
     */
    public static function date($date, $format = 'Y-m-d', $timezone = null)
    {
        $timezone = $timezone ?? self::getUserTimezone();
        
        if ($date instanceof Carbon) {
            return $date->copy()->setTimezone($timezone)->format($format);
        }
        
        return Carbon::parse($date)->setTimezone($timezone)->format($format);
    }

    /**
     * Format datetime with user's timezone
     */
    public static function datetime($date, $format = 'Y-m-d H:i:s', $timezone = null)
    {
        $timezone = $timezone ?? self::getUserTimezone();
        
        if ($date instanceof Carbon) {
            return $date->copy()->setTimezone($timezone)->format($format);
        }
        
        return Carbon::parse($date)->setTimezone($timezone)->format($format);
    }

    /**
     * Format time with user's timezone
     */
    public static function time($date, $format = 'H:i:s', $timezone = null)
    {
        $timezone = $timezone ?? self::getUserTimezone();
        
        if ($date instanceof Carbon) {
            return $date->copy()->setTimezone($timezone)->format($format);
        }
        
        return Carbon::parse($date)->setTimezone($timezone)->format($format);
    }

    /**
     * Get user's timezone from settings
     */
    private static function getUserTimezone()
    {
        if (auth()->check()) {
            return auth()->user()->timezone;
        }
        return 'Asia/Kabul'; // Default
    }

    /**
     * Get all available currencies
     */
    public static function getAvailableCurrencies()
    {
        return collect(self::$currencyNames)->map(function ($name, $code) {
            return [
                'code' => $code,
                'name' => $name,
                'symbol' => self::$currencySymbols[$code] ?? $code,
            ];
        })->sortBy('name')->values();
    }

    /**
     * Get all available timezones
     */
    public static function getAvailableTimezones()
    {
        return [
            'Asia/Kabul' => 'Afghanistan (UTC+4:30)',
            'Asia/Tehran' => 'Iran (UTC+3:30)',
            'Asia/Dubai' => 'UAE (UTC+4:00)',
            'Asia/Riyadh' => 'Saudi Arabia (UTC+3:00)',
            'Asia/Karachi' => 'Pakistan (UTC+5:00)',
            'Asia/Kolkata' => 'India (UTC+5:30)',
            'Asia/Shanghai' => 'China (UTC+8:00)',
            'Asia/Tokyo' => 'Japan (UTC+9:00)',
            'Europe/London' => 'United Kingdom (UTC+0:00)',
            'Europe/Paris' => 'France (UTC+1:00)',
            'America/New_York' => 'US Eastern (UTC-5:00)',
            'America/Los_Angeles' => 'US Pacific (UTC-8:00)',
            'Australia/Sydney' => 'Australia (UTC+10:00)',
            'UTC' => 'UTC (UTC+0:00)',
        ];
    }
}
