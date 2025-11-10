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
        Schema::table('team_feasibility_checklist_items', function (Blueprint $table) {
            // Drop the existing boolean column
            $table->dropColumn('checkbox_value');
        });

        Schema::table('team_feasibility_checklist_items', function (Blueprint $table) {
            // Add the column back as varchar(255)
            $table->string('checkbox_value', 255)->nullable()->after('is_checkbox');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_feasibility_checklist_items', function (Blueprint $table) {
            // Drop the varchar column
            $table->dropColumn('checkbox_value');
        });

        Schema::table('team_feasibility_checklist_items', function (Blueprint $table) {
            // Add it back as boolean
            $table->boolean('checkbox_value')->nullable()->after('is_checkbox');
        });
    }
};
