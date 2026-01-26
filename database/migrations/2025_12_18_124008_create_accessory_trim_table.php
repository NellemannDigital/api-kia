<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Trim;
use App\Models\Accesory;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accessory_trim', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Trim::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Accesory::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accessory_trim');
    }
};
