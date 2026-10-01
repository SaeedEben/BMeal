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
        Schema::create('recipe_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->foreignUuid('recipe_id')->constrained('recipes')->cascadeOnDelete();
            $table->unsignedSmallInteger('step_number');
            $table->text('instruction');
            $table->string('image', 500)->nullable();

            $table->unique(['recipe_id', 'step_number'], 'unique_recipe_step');

            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_steps');
    }
};
