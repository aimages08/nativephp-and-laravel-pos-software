<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'umar@test.com'],
            [
                'name' => 'Umar',
                'password' => 'umar',
            ]
        );
        echo "User ready: umar@test.com\n";
    }
}