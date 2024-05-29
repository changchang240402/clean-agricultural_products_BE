<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'shipping_money') && !Schema::hasColumn('orders', 'cost')) {
                $table->decimal('cost', 12, 2)->default(0);
                $table->decimal('shipping_money', 12, 2)->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'shipping_money') && Schema::hasColumn('orders', 'cost')) {
                $table->dropColumn('cost');
                $table->dropColumn('shipping_money');
            }
        });
    }
};
