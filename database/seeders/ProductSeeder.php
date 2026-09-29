<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::firstOrCreate(
            ['sku' => 'SAMPLE-001'],
            [
                'name' => 'Sample Product',
                'barcode' => '1234567890123',
                'cost' => 100,
                'price' => 150,
                'stock' => 50,
                'low_stock_threshold' => 10,
                'unit' => 'pcs',
                'is_active' => true,
            ]
        );
    }
}