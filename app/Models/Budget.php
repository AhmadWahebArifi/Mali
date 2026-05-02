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

    /**
     * Dynamically calculate spent amount for this budget
     */
    public function getSpentAttribute()
    {
        return $this->calculateSpent();
    }

    /**
     * Dynamically calculate current balance for this budget
     */
    public function getCurrentBalanceAttribute()
    {
        return $this->amount - $this->calculateSpent();
    }

    /**
     * Core calculation: sum expenses for this budget with proper joins and filters
     */
    private function calculateSpent()
    {
        $query = Transaction::selectRaw('COALESCE(SUM(transactions.amount), 0) as total')
            ->join('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->where('accounts.user_id', $this->user_id)
            ->where('transactions.type', 'expense')
            ->where(function ($q) {
                $q->whereNull('transactions.category_id')
                  ->orWhere('transactions.category_id', $this->category_id);
            })
            ->where(function ($q) {
                $q->whereNull('transactions.account_id')
                  ->orWhere('transactions.account_id', $this->account_id);
            });

        // Apply date filter based on budget period
        switch ($this->period) {
            case 'monthly':
                $query->whereMonth('transactions.date', now()->month)
                      ->whereYear('transactions.date', now()->year);
                break;
            case 'yearly':
                $query->whereYear('transactions.date', now()->year);
                break;
            case 'custom':
                if ($this->start_date && $this->end_date) {
                    $query->whereBetween('transactions.date', [$this->start_date, $this->end_date]);
                }
                break;
        }

        
        // Debug: Log the query and result for troubleshooting
        \Log::info('Budget calculateSpent debug', [
            'budget_id' => $this->id,
            'budget_name' => $this->name,
            'budget_user_id' => $this->user_id,
            'budget_category_id' => $this->category_id,
            'budget_account_id' => $this->account_id,
            'budget_period' => $this->period,
            'budget_start_date' => $this->start_date,
            'budget_end_date' => $this->end_date,
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings(),
            'result' => $query->value('total') ?? 0
        ]);

        return $query->value('total') ?? 0;
    }
}
