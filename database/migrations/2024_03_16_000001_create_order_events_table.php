<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_events', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 20);
            $table->string('event_type', 50);
            $table->json('event_data');
            $table->string('triggered_by')->nullable();
            $table->timestamp('occurred_at');

            $table->index('order_id');
            $table->index('event_type');
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_events');
    }
}; 