<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'store_name' => 'My POS Shop',
            'store_address' => '123 Main Street',
            'store_phone' => '+92 300 0000000',
            'currency_symbol' => 'Rs',
            'currency_code' => 'PKR',
            'tax_rate' => '17',
            'tax_included' => '0',
            'low_stock_threshold' => '5',
            'receipt_footer' => 'Thank you for shopping!',
        ];

        foreach ($defaults as $key => $value) {
            Setting::set($key, $value);
        }
    }
}