<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Car;
use App\Models\Trim;
use App\Models\Powertrain;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();

            $table->integer('struct_id')->unique();
            $table->foreignIdFor(Car::class);
            $table->foreignIdFor(Trim::class);
            $table->foreignIdFor(Powertrain::class);

            $table->string('model_code');
            $table->string('grade');
            $table->string('ocn')->nullable();
            $table->boolean('is_standard_configuration')->default(false);
            $table->json('model')->nullable();
            $table->string('year')->nullable();
            $table->json('variant')->nullable();
            $table->string('trim')->nullable();
            $table->json('engine')->nullable();
            $table->json('transmission')->nullable();
            $table->json('technical_specifications')->nullable();

            $table->string('model_change_code')->nullable();
            $table->string('original_model_change_code')->nullable();
            $table->string('referenced_foundation_car_id')->nullable();
            $table->string('referenced_foundation_trim_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
