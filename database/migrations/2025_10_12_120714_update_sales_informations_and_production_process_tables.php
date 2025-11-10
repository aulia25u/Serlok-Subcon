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
        // Update sales_informations table
        Schema::table('sales_informations', function (Blueprint $table) {
            // 1. Add new part_type column (radio button: critical_safety or regular_part)
            $table->enum('part_type', ['critical_safety', 'regular_part'])->nullable()->after('tools_depreciation');

            // 2. Change model from JSON to VARCHAR
            $table->string('model')->nullable()->change();

            // Drop old columns
            $table->dropColumn(['critical_safety', 'regular_part']);
        });

        // Update production_process_informations table
        Schema::table('production_process_informations', function (Blueprint $table) {
            // 3. Change default process_location to 'out_house'
            $table->enum('process_location', ['in_house', 'out_house'])->default('out_house')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback production_process_informations table
        Schema::table('production_process_informations', function (Blueprint $table) {
            $table->enum('process_location', ['in_house', 'out_house'])->default('in_house')->change();
        });

        // Rollback sales_informations table
        Schema::table('sales_informations', function (Blueprint $table) {
            // Restore old columns
            $table->boolean('critical_safety')->default(false);
            $table->boolean('regular_part')->default(false);

            // Change model back to JSON
            $table->json('model')->nullable()->change();

            // Drop new column
            $table->dropColumn('part_type');
        });
    }
};
