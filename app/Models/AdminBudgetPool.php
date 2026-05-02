<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminBudgetPool extends Model
{
    protected $fillable = [
        'total_allocated',
        'total_budget',
        'available_funds',
        'description'
    ];

    protected $casts = [
        'total_allocated' => 'decimal:2',
        'total_budget' => 'decimal:2',
        'available_funds' => 'decimal:2'
    ];

    /**
     * Get the current admin budget pool (singleton pattern)
     */
    public static function getCurrent()
    {
        try {
            return self::firstOrCreate([], [
                'total_allocated' => 0,
                'total_budget' => 0,
                'available_funds' => 0, // Will be calculated dynamically
                'description' => 'Admin budget pool for user allocations'
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to get or create admin budget pool: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            throw new \Exception('Failed to initialize admin budget pool: ' . $e->getMessage());
        }
    }

    /**
     * Get available funds dynamically (total_budget - total_allocated)
     */
    public function getAvailableFundsAttribute()
    {
        return max(0, $this->total_budget - $this->total_allocated);
    }

    /**
     * Check if admin can allocate the specified amount
     */
    public function canAllocate($amount)
    {
        return $this->available_funds >= $amount;
    }

    /**
     * Allocate budget to a user
     */
    public function allocateBudget($amount, $description = null)
    {
        if (!$this->canAllocate($amount)) {
            throw new \Exception('Insufficient funds in admin budget pool');
        }

        $this->total_allocated += $amount;
        // available_funds is calculated dynamically, do not set manually
        $this->save();

        return true;
    }

    /**
     * Return budget to pool when budget is deleted
     */
    public function returnBudget($amount)
    {
        $this->total_allocated -= $amount;
        // available_funds is calculated dynamically, do not set manually
        $this->save();

        return true;
    }

    /**
     * Add funds to admin budget pool
     */
    public function addFunds($amount)
    {
        if ($amount <= 0) {
            throw new \Exception('Amount must be greater than 0');
        }

        $this->total_budget += $amount;
        // available_funds is calculated dynamically, do not set manually
        
        if (!$this->save()) {
            throw new \Exception('Failed to save admin budget pool');
        }

        return true;
    }

    /**
     * Get allocation percentage
     */
    public function getAllocationPercentageAttribute()
    {
        if ($this->total_budget == 0) return 0;
        return ($this->total_allocated / $this->total_budget) * 100;
    }

    /**
     * Get remaining percentage
     */
    public function getRemainingPercentageAttribute()
    {
        return 100 - $this->allocation_percentage;
    }
}
