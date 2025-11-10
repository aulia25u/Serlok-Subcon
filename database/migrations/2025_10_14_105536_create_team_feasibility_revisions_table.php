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
        Schema::create('team_feasibility_revisions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feasibility_commitment_id');

            $table->string('revision_number', 10); // e.g., "01", "02", "03"
            $table->date('revision_date');
            $table->text('revision_contains'); // Description of what changed
            $table->foreignId('revised_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();

            $table->timestamps();

            // Foreign key with custom short name
            $table->foreign('feasibility_commitment_id', 'fk_revision_feas_id')
                  ->references('id')->on('team_feasibility_commitments')
                  ->cascadeOnDelete();

            // Indexes
            $table->index('feasibility_commitment_id', 'idx_feas_comm_rev');
            $table->index('revision_number', 'idx_rev_number');
            $table->unique(['feasibility_commitment_id', 'revision_number'], 'unq_feas_rev');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_feasibility_revisions');
    }
};
