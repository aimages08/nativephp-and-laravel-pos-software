<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('customer_payments', 'deleted_at')) {
            Schema::table('customer_payments', function (Blueprint $t) {
                $t->softDeletes();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('customer_payments', 'deleted_at')) {
            Schema::table('customer_payments', function (Blueprint $t) {
                $t->dropSoftDeletes();
            });
        }
    }
};