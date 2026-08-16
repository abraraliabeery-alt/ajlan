<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_media', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained()->nullOnDelete();
            $table->string('collection', 40)->default('warehouses');
            $table->string('type', 20);
            $table->string('file_path');
            $table->string('thumbnail_path')->nullable();
            $table->boolean('is_cover')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('property_media_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('property_media_id')->constrained('property_media')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('alt_text');
            $table->timestamps();
            $table->unique(['property_media_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_media_translations');
        Schema::dropIfExists('property_media');
    }
};
