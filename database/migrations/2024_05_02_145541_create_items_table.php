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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('item_name', 150);
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('seller_id');
            $table->text('describe');
            $table->integer('total');
            $table->decimal('price', 10, 2);
            $table->integer('type');
            $table->decimal('price_type', 15, 2);
            $table->string('image');
            $table->tinyInteger('status');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('seller_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
