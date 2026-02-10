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
        Schema::create('compliance_text_templates', function (Blueprint $table) {
            $table->id();

            $table->string('scope');
            $table->unsignedBigInteger('scope_id')->nullable();

            $table->string('variant');
            $table->text('template');
            $table->string('version');
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['scope', 'scope_id', 'variant', 'active']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
