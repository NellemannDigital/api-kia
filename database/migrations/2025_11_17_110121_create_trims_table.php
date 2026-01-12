<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Car;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trims', function (Blueprint $table) {
            $table->id();

            $table->integer('struct_id')->unique();
            $table->foreignIdFor(Car::class);

            $table->string('name');
            $table->integer('sort_order')->nullable();
            $table->integer('leasing_return_fee')->nullable();
            $table->json('primary_image')->nullable();
            $table->json('interior')->nullable();
            $table->json('technical_specifications')->nullable();
            $table->json('campaign')->nullable();
            $table->json('channels');
            $table->json('accessory_mapping')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trims');
    }
};
