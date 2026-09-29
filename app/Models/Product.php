<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

use SoftDeletes;

   protected $fillable = [
    'name', 'sku', 'barcode', 'barcode_type', 'plu', 'category_id', 'supplier_id',
    'cost', 'price', 'stock', 'expiry_date',
    'low_stock_threshold', 'unit', 'image', 'is_active',
];

    protected $casts = [
        'is_active' => 'boolean',
        'cost' => 'decimal:2',
        'price' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeLowStock($q)
    {
        return $q->whereColumn('stock', '<=', 'low_stock_threshold');
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock <= $this->low_stock_threshold;
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isFuture() && $this->expiry_date->diffInDays(now()) <= 30;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public static function generateBarcode(): string
    {
        do {
            $code = 'P'.str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT);
        } while (self::where('barcode', $code)->exists());

        return $code;
    }
}
