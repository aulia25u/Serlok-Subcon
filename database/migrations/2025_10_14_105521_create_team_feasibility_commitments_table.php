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
        Schema::create('team_feasibility_commitments', function (Blueprint $table) {
            $table->id();
            $table->string('document_no')->unique();
            $table->string('part_name');
            $table->string('part_no');
            $table->string('model')->nullable();
            $table->string('customer_name')->nullable(); // Changed from foreign key to simple string

            // Conclusion
            $table->enum('conclusion_status', ['feasible', 'feasible_with_changes', 'not_feasible'])->nullable();
            $table->text('conclusion_notes')->nullable();

            // Status workflow
            $table->enum('status', ['draft', 'in_review', 'approved', 'rejected'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');

            // Sign-off fields - dengan nama FK pendek
            $table->foreignId('general_mgr_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('general_mgr_signed_at')->nullable();

            $table->foreignId('factory_mgr_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('factory_mgr_signed_at')->nullable();

            $table->foreignId('qa_mgr_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('qa_mgr_signed_at')->nullable();

            $table->foreignId('qc_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('qc_signed_at')->nullable();

            $table->foreignId('engineering_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('engineering_signed_at')->nullable();

            $table->foreignId('production_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('production_signed_at')->nullable();

            $table->foreignId('maintenance_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('maintenance_signed_at')->nullable();

            $table->foreignId('ppic_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('ppic_signed_at')->nullable();

            $table->foreignId('purchasing_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('purchasing_signed_at')->nullable();

            $table->foreignId('sales_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('sales_signed_at')->nullable();

            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // Indexes
            $table->index('document_no');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_feasibility_commitments');
    }
};
