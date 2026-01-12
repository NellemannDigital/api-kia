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
        Schema::create('dealers', function (Blueprint $table) {
            $table->id();
            $table->string('dynamics_id')->unique();
            $table->string('account_number')->unique();

            $table->string('company_id')->nullable();
            $table->string('crm_id')->nullable();
            $table->string('dealerbridge_id')->nullable();
            $table->string('bilinfo_id')->nullable();
            $table->string('autouncle_department_id')->nullable();
            $table->string('rooftop_id')->nullable();

            $table->string('dealer_guid')->nullable();
            $table->string('owner_guid')->nullable();

            $table->json('channels')->nullable();

            $table->string('name');
            $table->string('display_name')->nullable();
            $table->integer('cvr_number')->nullable();
            $table->string('group')->nullable();

            $table->string('street_name')->nullable();
            $table->string('street_number')->nullable();
            $table->string('city')->nullable();
            $table->integer('zip_code')->nullable();
            $table->string('country')->nullable();

            $table->float('latitude')->nullable();
            $table->float('longitude')->nullable();

            $table->string('phone')->nullable();
            $table->json('emails')->nullable();
            $table->json('urls')->nullable();

            $table->json('types')->nullable();
            $table->json('opening_hours')->nullable(); // sales, workshop
            $table->json('postal_codes')->nullable(); // b2c, b2b

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealers');
    }
};
