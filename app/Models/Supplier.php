<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{

use SoftDeletes;

    protected $fillable = ['name', 'phone', 'email', 'address', 'balance', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'balance' => 'decimal:2'];

    public function products() { return $this->hasMany(Product::class); }
    public function purchases() { return $this->hasMany(Purchase::class); }
}