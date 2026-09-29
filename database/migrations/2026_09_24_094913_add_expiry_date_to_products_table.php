<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

            public function up(): void
            {
                if (Schema::hasColumn('products', 'expiry_date')) {
                    return; // already exists, skip
                }
                Schema::table('products', function (Blueprint $table) {
                    $table->date('expiry_date')->nullable()->after('stock');
                });
            }

            public function down(): void
            {
                if (Schema::hasColumn('products', 'expiry_date')) {
                    Schema::table('products', function (Blueprint $table) {
                        $table->dropColumn('expiry_date');
                    });
                }
            }


};