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
        Schema::create('accessories', function (Blueprint $table) {
            $table->id();
            $table->integer('struct_id')->unique();

            $table->string('name');
            $table->text('description')->nullable();
            $table->string('part_number')->nullable();
            $table->string('category_one')->nullable();
            $table->string('category_two')->nullable();
            $table->string('disclaimer')->nullable();
            $table->json('primary_image')->nullable();
            $table->json('override_image')->nullable();
            $table->json('prices')->nullable();
            $table->json('additional_images')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accessories');
    }
};
