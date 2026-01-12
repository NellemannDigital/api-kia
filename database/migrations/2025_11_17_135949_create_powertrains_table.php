<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Trim;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('powertrains', function (Blueprint $table) {
            $table->id();

            $table->integer('configuration_id');
            $table->foreignIdFor(Trim::class)->cascadeOnDelete();

            $table->string('ocn')->nullable();
            $table->boolean('leasing_active')->default(false);
            $table->json('engine')->nullable();
            $table->json('transmission')->nullable();
            $table->json('technical_specifications')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('powertrains');
    }
};
