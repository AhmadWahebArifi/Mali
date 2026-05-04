<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetAssignment extends Model
{
    protected $fillable = [
        'user_id',
        'budget_id',
        'account_id',
        'assigned_amount',
        'remaining_amount',
        'assignment_notes',
        'assigned_at',
        'status',
    ];

    protected $casts = [
        'assigned_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'assigned_at' => 'datetime',
    ];

    /**
     * Get the user that owns the budget assignment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the budget that owns the assignment.
     */
    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    /**
     * Get the account that owns the assignment.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Scope to get active assignments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get the spent amount from the budget.
     */
    public function getSpentAmountAttribute(): float
    {
        return $this->assigned_amount - $this->remaining_amount;
    }

    /**
     * Update remaining amount based on budget spending.
     */
    public function updateRemainingAmount(): void
    {
        $budgetSpent = \App\Models\Transaction::where('budget_id', $this->budget_id)
            ->where('type', 'expense')
            ->sum('amount');
        
        $this->remaining_amount = max(0, $this->assigned_amount - $budgetSpent);
        $this->save();
    }
}
