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
        Schema::create('colors', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Trim::class)->cascadeOnDelete();

            $table->string('code');
            $table->string('primary_color');
            $table->string('secondary_color')->nullable();
            $table->string('type')->nullable();
            $table->json('color_image')->nullable();
            $table->string('ocn_change_code')->nullable();

            $table->json('turntable_images')->nullable();

            $table->timestamps();

            $table->unique(['trim_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colors');
    }
};
