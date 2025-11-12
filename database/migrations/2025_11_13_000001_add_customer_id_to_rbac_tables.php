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
        Schema::table('depts', function (Blueprint $table) {
            $table->foreignId('customer_id')
                ->nullable()
                ->after('dept_name')
                ->constrained('customers')
                ->nullOnDelete();
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->foreignId('customer_id')
                ->nullable()
                ->after('dept_id')
                ->constrained('customers')
                ->nullOnDelete();
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->foreignId('customer_id')
                ->nullable()
                ->after('section_id')
                ->constrained('customers')
                ->nullOnDelete();
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->foreignId('customer_id')
                ->nullable()
                ->after('role_name')
                ->constrained('customers')
                ->nullOnDelete();
        });

        Schema::table('plants', function (Blueprint $table) {
            $table->foreignId('customer_id')
                ->nullable()
                ->after('plant_name')
                ->constrained('customers')
                ->nullOnDelete();
        });

        Schema::table('user_details', function (Blueprint $table) {
            $table->foreignId('customer_id')
                ->nullable()
                ->after('role_id')
                ->constrained('customers')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });

        Schema::table('plants', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });

        Schema::table('depts', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }
};
