<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Powertrain;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leasing_prices', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Powertrain::class)->cascadeOnDelete();

            $table->decimal('down_payment', 10, 2)->nullable();
            $table->decimal('mp_10000', 10, 2)->nullable();
            $table->decimal('mp_15000', 10, 2)->nullable();
            $table->decimal('mp_20000', 10, 2)->nullable();
            $table->decimal('mp_25000', 10, 2)->nullable();
            $table->decimal('mp_30000', 10, 2)->nullable();
            $table->decimal('mp_35000', 10, 2)->nullable();
            $table->decimal('mp_40000', 10, 2)->nullable();

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
        Schema::dropIfExists('leasing_prices');
    }
};
