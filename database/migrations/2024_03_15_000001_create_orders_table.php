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
            $table->id()->primary();
            $table->string('order_id', 20); // Order-YYYYMMDD-NNN
            $table->string('ec_site_code', 20);
            $table->string('status', 20); // pending, failed, unshipped
            $table->timestamp('ordered_at');
            $table->timestamp('canceled_at')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->string('customer_address');
            $table->integer('shipping_fee_with_tax');
            $table->integer('shipping_fee_without_tax');
            $table->float('shipping_fee_tax_rate');
            $table->integer('total_amount_with_tax');
            $table->integer('total_amount_without_tax');
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();

            // インデックス
            $table->index('order_id');
            $table->index('ec_site_code');
            $table->foreign('ec_site_code')
                ->references('code')
                ->on('ec_sites')
                ->onDelete('restrict');
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