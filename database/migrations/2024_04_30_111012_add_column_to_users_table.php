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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 12)->unique();
            $table->string('address', 100);
            $table->date('birthday')->nullable();
            $table->string('license_plates', 10)->unique()->nullable();
            $table->string('driving_license_number', 12)->unique()->nullable();
            $table->string('vehicles', 50)->nullable();
            $table->integer('payload')->nullable();
            $table->string('avatar')->nullable();
            $table->tinyInteger('role');
            $table->tinyInteger('status')->default(1);
            if (Schema::hasColumn('users', 'name')) {
                $table->string('name', 50)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('address');
            $table->dropColumn('birthday');
            $table->dropColumn('license_plates');
            $table->dropColumn('driving_license_number');
            $table->dropColumn('vehicles');
            $table->dropColumn('payload');
            $table->dropColumn('avatar');
            $table->dropColumn('role');
            $table->dropColumn('status');
            if (Schema::hasColumn('users', 'name')) {
                $table->string('name')->change();
            }
        });
    }
};
