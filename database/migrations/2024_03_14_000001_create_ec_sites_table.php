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
        Schema::create('ec_sites', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('ECサイトコード');
            $table->string('name', 100)->comment('ECサイト名');
            $table->text('description')->nullable()->comment('説明');
            $table->boolean('is_active')->default(true)->comment('有効フラグ');
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();

            // インデックス
            $table->index('code');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ec_sites');
    }
}; 