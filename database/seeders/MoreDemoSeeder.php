<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class MoreDemoSeeder extends Seeder
{
    public function run(): void
    {
        echo "Adding bulk demo data...\n";

        // 50 more categories
        for ($i = 1; $i <= 50; $i++) {
            Category::firstOrCreate(
                ['name' => "Category $i"],
                ['description' => "Auto category #$i", 'is_active' => true]
            );
        }

        // 100 more customers
        for ($i = 1; $i <= 100; $i++) {
            Customer::firstOrCreate(
                ['name' => "Customer $i"],
                ['phone' => '+92 300 ' . str_pad((string) $i, 7, '0', STR_PAD_LEFT), 'is_active' => true]
            );
        }

        // 30 more suppliers
        for ($i = 1; $i <= 30; $i++) {
            Supplier::firstOrCreate(
                ['name' => "Supplier Co $i"],
                ['phone' => '+92 300 ' . str_pad((string) $i, 7, '0', STR_PAD_LEFT), 'is_active' => true]
            );
        }

        // 100 more products
        $catIds = Category::pluck('id')->toArray();
        for ($i = 1; $i <= 100; $i++) {
            $cost = rand(50, 500);
            Product::firstOrCreate(
                ['sku' => "BULK-$i"],
                [
                    'name' => "Bulk Product $i",
                    'barcode' => 'BULK' . str_pad((string) $i, 10, '0', STR_PAD_LEFT),
                    'barcode_type' => 'generated',
                    'plu' => (string) (200 + $i),
                    'category_id' => $catIds[array_rand($catIds)],
                    'cost' => $cost,
                    'price' => $cost + rand(30, 200),
                    'stock' => rand(10, 200),
                    'low_stock_threshold' => 10,
                    'unit' => 'pcs',
                    'is_active' => true,
                ]
            );
        }

        // 60 more expenses
        $user = User::first();
        for ($i = 1; $i <= 60; $i++) {
            Expense::create([
                'title' => "Expense #$i",
                'amount' => rand(500, 20000),
                'category' => ['Rent', 'Utilities', 'Salary', 'Maintenance', 'Supplies'][rand(0, 4)],
                'expense_date' => now()->subDays(rand(1, 60))->format('Y-m-d'),
                'user_id' => $user->id,
            ]);
        }

        echo "✅ More data added: 50 cats, 100 customers, 30 suppliers, 100 products, 60 expenses\n";
    }
}