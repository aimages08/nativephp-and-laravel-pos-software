<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'supplier_return' reference columns to stock movements (already has notes)
        // Add 'wastage' expense category — no migration needed, uses existing structure

        // Add 'reason' field for better tracking
        if (!Schema::hasColumn('stock_movements', 'reason')) {
            Schema::table('stock_movements', function (Blueprint $table) {
                $table->string('reason')->nullable()->after('type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('stock_movements', 'reason')) {
            Schema::table('stock_movements', function (Blueprint $table) {
                $table->dropColumn('reason');
            });
        }
    }
};