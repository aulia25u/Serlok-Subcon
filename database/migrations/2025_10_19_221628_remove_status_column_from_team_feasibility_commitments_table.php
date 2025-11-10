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
        Schema::table('team_feasibility_commitments', function (Blueprint $table) {
            // Drop index first before dropping column
            $table->dropIndex(['status']);
            // Drop status column
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_feasibility_commitments', function (Blueprint $table) {
            // Re-add status column if rollback
            $table->enum('status', ['draft', 'in_review', 'approved', 'rejected'])->default('draft')->after('conclusion_notes');
            // Re-add index
            $table->index('status');
        });
    }
};
