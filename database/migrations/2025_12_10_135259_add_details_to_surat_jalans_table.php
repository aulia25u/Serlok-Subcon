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
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->foreignId('employee_job_id')->nullable()->constrained('employee_jobs')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('master_customers')->onDelete('set null');
            $table->string('known_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->dropForeign(['employee_job_id']);
            $table->dropColumn('employee_job_id');
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
            $table->dropColumn('known_by');
        });
    }
};
