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
        Schema::create('prices', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Powertrain::class)->cascadeOnDelete();

            $table->decimal('dealer_net_price', 12, 2)->nullable();
            $table->decimal('dealer_profit', 12, 2)->nullable();
            $table->decimal('minimum_dealer_profit', 12, 2)->nullable();
            $table->decimal('campaign_dealer_profit', 12, 2)->nullable();
            $table->decimal('suggested_retail_price', 12, 2)->nullable();
            $table->decimal('campaign_retail_price', 12, 2)->nullable();
            $table->decimal('van_conversion_price', 12, 2)->nullable();
            $table->decimal('van_price_vat', 12, 2)->nullable();
            $table->decimal('van_price', 12, 2)->nullable();
            $table->decimal('fleet_net_price', 12, 2)->nullable();

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
        Schema::dropIfExists('prices');
    }
};
