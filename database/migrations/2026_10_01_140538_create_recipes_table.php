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
        Schema::create('recipes', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('country_id')->constrained('countries')->nullOnDelete();

            $table->string('name', 200);
            $table->string('slug', 220)->unique();
            $table->text('description')->nullable();
            $table->string('image', 500)->nullable();
            $table->string('meal_type', 100)->nullable();

            $table->unsignedSmallInteger('prep_time_minutes')->nullable();
            $table->unsignedSmallInteger('cook_time_minutes')->nullable();
            $table->unsignedSmallInteger('servings')->nullable();

            $table->enum('difficulty', ['easy', 'medium', 'hard'])->nullable()->default('easy');
            $table->decimal('calories_per_serving', 8, 2)->nullable();

            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
