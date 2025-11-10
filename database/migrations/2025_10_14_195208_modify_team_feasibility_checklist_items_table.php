<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop foreign key first if exists
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'team_feasibility_checklist_items'
            AND REFERENCED_TABLE_NAME IS NOT NULL
            AND COLUMN_NAME = 'parent_item_id'
        ");

        if (! empty($foreignKeys)) {
            Schema::table('team_feasibility_checklist_items', function (Blueprint $table) {
                $table->dropForeign('fk_checklist_parent_id');
            });
        }

        Schema::table('team_feasibility_checklist_items', function (Blueprint $table) {
            // Drop columns: parent_item_id, sub_point, item_number
            $table->dropColumn(['parent_item_id', 'sub_point', 'item_number']);

            // Drop and recreate checkbox_value as boolean
            $table->dropColumn('checkbox_value');
            $table->boolean('checkbox_value')->nullable()->after('is_checkbox');

            // Rename columns to English
            $table->renameColumn('point_cek', 'checkpoint_description');
            $table->renameColumn('keterangan', 'notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_feasibility_checklist_items', function (Blueprint $table) {
            // Rename back to Indonesian
            $table->renameColumn('checkpoint_description', 'point_cek');
            $table->renameColumn('notes', 'keterangan');

            // Restore checkbox_value as string
            $table->dropColumn('checkbox_value');
            $table->string('checkbox_value')->nullable()->after('is_checkbox');

            // Restore dropped columns
            $table->string('item_number', 20)->after('feasibility_commitment_id');
            $table->string('sub_point')->nullable()->after('point_cek');
            $table->unsignedBigInteger('parent_item_id')->nullable()->after('order_sequence');

            // Restore foreign key
            $table->foreign('parent_item_id', 'fk_checklist_parent_id')
                ->references('id')->on('team_feasibility_checklist_items')
                ->cascadeOnDelete();
        });
    }
};
