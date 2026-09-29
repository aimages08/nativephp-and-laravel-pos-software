<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{

    use SoftDeletes;

    protected $fillable = ['title', 'amount', 'category', 'expense_date', 'notes', 'user_id'];
    protected $casts = ['amount' => 'decimal:2', 'expense_date' => 'date'];

    public function user() { return $this->belongsTo(User::class); }
}