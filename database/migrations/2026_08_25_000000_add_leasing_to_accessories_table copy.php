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
        Schema::table('accessories', function (Blueprint $table) {
            $table->boolean('leasing_active')->default(false);
            $table->decimal('leasing_price', 12, 2)->nullable();
            $table->decimal('leasing_down_payment', 12, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accessories', function (Blueprint $table) {
            $table->dropColumn('leasing_active');
            $table->dropColumn('leasing_price');
            $table->dropColumn('leasing_down_payment');
        });
    }
};
