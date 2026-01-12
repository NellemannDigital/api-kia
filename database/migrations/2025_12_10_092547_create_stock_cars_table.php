<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Configuration;
use App\Models\Dealer;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_cars', function (Blueprint $table) {
            $table->id();
            $table->string('dynamics_id')->unique();
            $table->string('vehicle_number')->unique();

            $table->string('name');
            $table->string('struct_id');
            $table->string('vin')->nullable();
            $table->string('model_code')->nullable();
            $table->string('model_year')->nullable();
            $table->json('exterior')->nullable();
            $table->json('interior')->nullable();
            $table->string('equipment')->nullable();

            $table->foreignIdFor(Configuration::class)->nullable();
            $table->foreignIdFor(Dealer::class)->nullable()->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_cars');
    }
};
