<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'client_expense_id',
        'sync_status',
        'sync_error',
        'title',
        'amount',
        'category',
        'expense_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'float',
        'expense_date' => 'datetime',
    ];
}
