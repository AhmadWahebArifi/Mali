<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'type',
        'amount',
        'account_id',
        'category_id',
        'description',
        'date',
        'created_by',
        'is_over_budget',
        'outstanding_amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
        'is_over_budget' => 'boolean',
        'outstanding_amount' => 'decimal:2'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
