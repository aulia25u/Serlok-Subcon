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
        Schema::table('sales_informations', function (Blueprint $table) {
            // Drop existing string columns
            $table->dropColumn(['approved_by', 'checked_by_1', 'checked_by_2', 'prepared_by']);
        });

        Schema::table('sales_informations', function (Blueprint $table) {
            // Add new foreign key columns
            $table->unsignedBigInteger('approved_by')->nullable()->after('decision');
            $table->unsignedBigInteger('checked_by_1')->nullable()->after('approved_by');
            $table->unsignedBigInteger('checked_by_2')->nullable()->after('checked_by_1');
            $table->unsignedBigInteger('prepared_by')->nullable()->after('checked_by_2');

            // Add foreign key constraints
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('checked_by_1')->references('id')->on('users')->onDelete('set null');
            $table->foreign('checked_by_2')->references('id')->on('users')->onDelete('set null');
            $table->foreign('prepared_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_informations', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['checked_by_1']);
            $table->dropForeign(['checked_by_2']);
            $table->dropForeign(['prepared_by']);

            // Drop foreign key columns
            $table->dropColumn(['approved_by', 'checked_by_1', 'checked_by_2', 'prepared_by']);
        });

        Schema::table('sales_informations', function (Blueprint $table) {
            // Restore original string columns
            $table->string('approved_by')->nullable()->after('decision');
            $table->string('checked_by_1')->nullable()->after('approved_by');
            $table->string('checked_by_2')->nullable()->after('checked_by_1');
            $table->string('prepared_by')->nullable()->after('checked_by_2');
        });
    }
};
