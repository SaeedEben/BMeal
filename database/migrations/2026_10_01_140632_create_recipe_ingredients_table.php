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
        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('recipe_id')->constrained('recipes')->nullOnDelete();
            $table->foreignUuid('ingredient_id')->constrained('ingredients')->nullOnDelete();

            $table->foreignUuid('unit_id')->nullable()->constrained('units')->nullOnDelete();

            $table->decimal('quantity', 10, 2)->nullable();
            
            $table->string('notes', 255)->nullable();
            $table->unsignedSmallInteger('sort_order')->nullable()->default(0);

            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique(['recipe_id', 'ingredient_id'], 'unique_recipe_ingredient');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_ingredients');
    }
};
