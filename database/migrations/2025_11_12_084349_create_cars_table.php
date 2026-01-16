<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();

            $table->integer('struct_id')->unique();
            $table->string('web_id')->nullable();

            $table->string('name');
            $table->string('year')->nullable();
            $table->string('delivery_year')->nullable();
            $table->text('disclaimer')->nullable();

            $table->json('model')->nullable();
            $table->json('variant')->nullable();
            $table->json('primary_image')->nullable();
            $table->json('technical_specifications')->nullable();
            $table->json('dimensions')->nullable();
            $table->json('campaign')->nullable();
            $table->json('urls')->nullable();
            $table->json('channels');
            $table->json('price_list')->nullable();
            $table->json('files')->nullable();
            $table->json('insurance_rates')->nullable();
            $table->json('categories')->nullable();
            
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
