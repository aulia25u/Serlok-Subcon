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
        Schema::create('master_inspections', function (Blueprint $table) {
            $table->id();
            $table->string('inspection_item', 255); // Inspection Item (e.g., DIAMETER 1, LENGTH, APPEARANCE)
            $table->string('inspection_method', 255); // Inspection Method/Equipment (e.g., CALIPER, RULER, VISUAL)
            $table->string('standard', 255); // Standard (e.g., Ø 101±0,8, 250±, NO DEFECT)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_inspections');
    }
};
