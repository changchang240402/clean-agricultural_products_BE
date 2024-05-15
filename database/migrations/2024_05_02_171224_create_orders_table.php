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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_price', 15, 2);
            $table->decimal('total_quantity', 10, 2);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('trader_id')->nullable();
            $table->string('status_review', 10)->default('1');
            $table->unsignedBigInteger('status');
            $table->dateTime('order_date')->nullable();
            $table->dateTime('delivery_date')->nullable();
            $table->dateTime('received_date')->nullable();
            $table->dateTime('order_cancellation_date')->nullable();
            $table->string('cancellation_note')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('seller_id')->references('id')->on('users');
            $table->foreign('trader_id')->references('id')->on('users');
            $table->foreign('status')->references('id')->on('order_types');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
