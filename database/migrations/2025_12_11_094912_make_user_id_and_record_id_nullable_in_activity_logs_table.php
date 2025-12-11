<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->unsignedBigInteger('record_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            // Note: Reverting nullable to non-nullable might fail if null values were inserted.
            // We adding a raw statement to handle data cleanup if strictly needed, 
            // but for standard rollback we just try to revert the schema.
            // In a real production rollback, you'd want to handle the nulls first.

            // Assuming we want to revert, we would need to ensure no nulls exist or delete them.
            // DB::table('activity_logs')->whereNull('user_id')->delete(); // Risky in auto-migration

            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->unsignedBigInteger('record_id')->nullable(false)->change();
        });
    }
};
