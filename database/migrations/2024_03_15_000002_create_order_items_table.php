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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('order_id', 20);
            $table->string('item_id', 20); // ITEM-NNNNN
            $table->string('name');
            $table->integer('price_with_tax');
            $table->integer('price_without_tax');
            $table->float('price_tax_rate');
            $table->integer('quantity');
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();

            // 外部キー制約
            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('orders')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
}; 