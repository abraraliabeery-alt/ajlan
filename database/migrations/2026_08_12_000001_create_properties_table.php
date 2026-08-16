<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('properties')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('type', 30)->default('industrial_plot');
            $table->string('status', 30)->default('contact');
            $table->decimal('land_area', 12, 2)->nullable();
            $table->decimal('built_area', 12, 2)->nullable();
            $table->decimal('min_unit_area', 12, 2)->nullable();
            $table->decimal('max_unit_area', 12, 2)->nullable();
            $table->decimal('side_height', 8, 2)->nullable();
            $table->decimal('middle_height', 8, 2)->nullable();
            $table->unsignedInteger('units_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
