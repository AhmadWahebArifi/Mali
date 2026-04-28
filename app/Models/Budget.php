<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'account_id',
        'name',
        'amount',
        'spent',
        'current_balance',
        'period',
        'start_date',
        'end_date',
        'is_active',
        'description'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'spent' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Category::class, 'id', 'category_id');
    }

    public function getRemainingAttribute()
    {
        return max(0, $this->amount - $this->spent);
    }

    public function getPercentageUsedAttribute()
    {
        if ($this->amount == 0) return 0;
        return min(100, ($this->spent / $this->amount) * 100);
    }

    public function getIsOverBudgetAttribute()
    {
        return $this->spent > $this->amount;
    }

    public function getIsNearLimitAttribute()
    {
        return $this->getPercentageUsedAttribute() >= 80;
    }

    public function updateSpentAmount()
    {
        if ($this->category_id) {
            // Calculate expenses for this budget period
            $expenses = Transaction::where('created_by', $this->user_id)
                ->where('category_id', $this->category_id)
                ->where('type', 'expense')
                ->when($this->period === 'monthly', function ($query) {
                    $query->whereMonth('date', now()->month)
                          ->whereYear('date', now()->year);
                })
                ->when($this->period === 'yearly', function ($query) {
                    $query->whereYear('date', now()->year);
                })
                ->when($this->period === 'custom' && $this->start_date && $this->end_date, function ($query) {
                    $query->whereBetween('date', [$this->start_date, $this->end_date]);
                })
                ->sum('amount');
            
            // Current balance = budget amount - expenses (remaining budget)
            $this->spent = $expenses;
            $this->current_balance = $this->amount - $expenses;
        } else {
            // For budgets without category (overall budget)
            // Calculate expenses for this budget period
            $expenses = Transaction::where('created_by', $this->user_id)
                ->where('type', 'expense')
                ->when($this->period === 'monthly', function ($query) {
                    $query->whereMonth('date', now()->month)
                          ->whereYear('date', now()->year);
                })
                ->when($this->period === 'yearly', function ($query) {
                    $query->whereYear('date', now()->year);
                })
                ->when($this->period === 'custom' && $this->start_date && $this->end_date, function ($query) {
                    $query->whereBetween('date', [$this->start_date, $this->end_date]);
                })
                ->sum('amount');
            
            // Current balance = budget amount - expenses (remaining budget)
            $this->spent = $expenses;
            $this->current_balance = $this->amount - $expenses;
        }
        
        $this->save();
    }
}
