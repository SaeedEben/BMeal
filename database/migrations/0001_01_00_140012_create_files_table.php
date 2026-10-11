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
        Schema::create('files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('file_type');
            $table->string('mime_type')->nullable();
            $table->bigInteger('size')->nullable();

            $table->text('storage_path');
            $table->string('storage_provider')->default('local');
            $table->text('external_url')->nullable();
            $table->char('sha256', 64)->nullable()->unique();

            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
