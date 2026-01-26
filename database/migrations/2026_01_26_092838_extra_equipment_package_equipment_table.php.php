<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ExtraEquipmentPackage;
use App\Models\Equipment;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('extra_equipment_package_equipment', function (Blueprint $table) {
            $table->id();
            
            $table->foreignIdFor(ExtraEquipmentPackage::class)
                ->constrained()
                ->cascadeOnDelete()
                ->name('eep_pkg_id_fk');
            
            $table->foreignIdFor(Equipment::class)
                ->constrained()
                ->cascadeOnDelete()
                ->name('eep_eq_id_fk');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extra_equipment_package_equipment');
    }
};