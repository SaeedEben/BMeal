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
        Schema::create('units', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name', 50);
            $table->string('symbol', 20);
            $table->string('slug', 60)->unique();
            $table->enum('type', ['weight', 'volume', 'count', 'length', 'temperature']);

            $table->decimal('conversion_factor', 12, 6)->nullable();
            $table->boolean('is_metric')->default(true);
            $table->string('status')->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
