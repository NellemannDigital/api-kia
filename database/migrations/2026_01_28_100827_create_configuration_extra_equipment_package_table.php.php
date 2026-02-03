<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Configuration;
use App\Models\ExtraEquipmentPackage;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configuration_extra_equipment_package', function (Blueprint $table) {
            $table->id();
            
            $table->foreignIdFor(Configuration::class);
            $table->foreignIdFor(ExtraEquipmentPackage::class);
                        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuration_extra_equipment_package');
    }
};
