<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Dealer;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('used_cars', function (Blueprint $table) {
            $table->id();

            $table->string('vehicle_id')->unique();
            $table->string('mileage')->nullable();
            $table->string('year')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('variant')->nullable();
            $table->string('registration_date')->nullable();

            $table->foreignIdFor(Dealer::class)->nullable()->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('used_cars');
    }
};
