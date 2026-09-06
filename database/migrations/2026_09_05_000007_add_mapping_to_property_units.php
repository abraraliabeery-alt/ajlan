<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_units', function (Blueprint $table): void {
            $table->string('code', 30)->nullable()->after('unit_number');
            $table->json('parcel_nos')->nullable()->after('area');
            $table->decimal('land_area', 12, 2)->nullable()->after('area');
        });
    }

    public function down(): void
    {
        Schema::table('property_units', function (Blueprint $table): void {
            $table->dropColumn(['code', 'parcel_nos', 'land_area']);
        });
    }
};
