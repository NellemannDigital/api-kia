<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Trim;
use App\Models\ExtraEquipmentPackage;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leasing_extra_equipment_packages', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Trim::class)->cascadeOnDelete();
            $table->foreignIdFor(ExtraEquipmentPackage::class);

            $table->string('code');
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('down_payment', 12, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leasing_extra_equipment_packages');
    }
};
