<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Trim;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('extra_equipment_packages', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Trim::class);

            $table->string('code');
            $table->string('name');
            $table->string('category')->nullable();
            $table->json('image')->nullable();

            $table->json('requires')->nullable();
            $table->json('excludes')->nullable();
            $table->json('excludes_standard_equipment')->nullable();
            $table->json('interior_override_to')->nullable();
            $table->json('model_change_code')->nullable();
            $table->json('engine_required')->nullable();
            $table->json('transmission_required')->nullable();
            $table->json('color_required')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extra_equipment_packages');
    }
};
