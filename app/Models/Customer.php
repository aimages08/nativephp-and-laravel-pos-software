<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

use SoftDeletes;

    protected $fillable = ['name', 'phone', 'email', 'address', 'balance', 'is_active'];

    protected $casts = ['is_active' => 'boolean', 'balance' => 'decimal:2'];

    public function sales() { return $this->hasMany(Sale::class); }
    public function payments() { return $this->hasMany(CustomerPayment::class); }

    public function getIsDebtorAttribute(): bool
    {
        return $this->balance > 0;
    }
}