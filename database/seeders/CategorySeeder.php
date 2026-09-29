<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $defaults = ['General', 'Food', 'Beverages', 'Electronics', 'Stationery'];
        foreach ($defaults as $name) {
            Category::firstOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}