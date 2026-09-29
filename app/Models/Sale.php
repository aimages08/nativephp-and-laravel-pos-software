<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{

use SoftDeletes;

    protected $fillable = [
        'invoice_no', 'customer_id', 'user_id',
        'subtotal', 'discount', 'tax', 'total',
        'paid', 'change_due', 'payment_method', 'status', 'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid' => 'decimal:2',
        'change_due' => 'decimal:2',
    ];

    public function items() { return $this->hasMany(SaleItem::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function user() { return $this->belongsTo(User::class); }

    public function getDueAttribute(): float
    {
        return max(0, (float) $this->total - (float) $this->paid);
    }

    public function scopeCredit($q)
    {
        return $q->where('payment_method', 'credit');
    }
}