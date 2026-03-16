<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Accessory;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accessory_prices', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Accessory::class)->cascadeOnDelete();

            $table->decimal('price', 12, 2)->nullable();

            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accessory_prices');
    }
};