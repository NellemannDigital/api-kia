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
        Schema::table('extra_equipment_packages', function (Blueprint $table) {
            $table->string('disclaimer')->nullable()->after('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('extra_equipment_packages', function (Blueprint $table) {
             $table->dropColumn('disclaimer');
        });
    }
};
