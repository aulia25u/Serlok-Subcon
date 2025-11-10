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
        Schema::table('additional_parts', function (Blueprint $table) {
            // Rename column from part_id to material_id
            $table->renameColumn('part_id', 'material_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('additional_parts', function (Blueprint $table) {
            // Rename column back from material_id to part_id
            $table->renameColumn('material_id', 'part_id');
        });
    }
};
