<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::firstOrCreate(
            ['name' => 'Walk-in Customer'],
            ['phone' => '-', 'is_active' => true]
        );
    }
}