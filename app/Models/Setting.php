<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    protected $fillable = [
        'user_id',
        'key',
        'value',
        'type',
        'category',
        'description'
    ];

    protected $casts = [
        'value' => 'string',
    ];

    /**
     * Get the user that owns the setting.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the typed value of the setting.
     */
    public function getTypedValue()
    {
        return match($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->value,
            'float' => (float) $this->value,
            'json' => json_decode($this->value, true),
            'array' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    /**
     * Set the typed value of the setting.
     */
    public function setTypedValue($value)
    {
        $this->value = match($this->type) {
            'boolean' => $value ? '1' : '0',
            'json', 'array' => json_encode($value),
            default => (string) $value,
        };
    }

    /**
     * Get a setting for a user.
     */
    public static function getSetting($userId, $key, $default = null)
    {
        $setting = static::where('user_id', $userId)->where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        return $setting->getTypedValue();
    }

    /**
     * Set a setting for a user.
     */
    public static function setSetting($userId, $key, $value, $type = 'string', $category = 'general', $description = null)
    {
        return static::updateOrCreate(
            ['user_id' => $userId, 'key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'category' => $category,
                'description' => $description,
            ]
        );
    }

    /**
     * Get all settings for a user grouped by category.
     */
    public static function getUserSettings($userId)
    {
        return static::where('user_id', $userId)
            ->get()
            ->groupBy('category');
    }
}
