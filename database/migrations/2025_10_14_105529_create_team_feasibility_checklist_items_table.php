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
        Schema::create('team_feasibility_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feasibility_commitment_id');

            $table->string('item_number', 20); // e.g., "1", "3.1", "4.2.1"
            $table->string('item_code', 100)->nullable(); // unique code for identification
            $table->text('point_cek'); // Question/point to check
            $table->string('sub_point')->nullable(); // Sub-point identifier

            // Check result
            $table->enum('check_result', ['ok', 'tidak_ok'])->nullable();
            $table->string('pic', 50)->nullable(); // ENG, SLS, PPIC, etc.
            $table->text('keterangan')->nullable(); // Notes/remarks

            // For checkbox items
            $table->boolean('is_checkbox')->default(false);
            $table->string('checkbox_value')->nullable(); // "checked" or null

            // Ordering and hierarchy
            $table->integer('order_sequence')->default(0);
            $table->unsignedBigInteger('parent_item_id')->nullable();

            $table->timestamps();

            // Foreign keys with custom short names
            $table->foreign('feasibility_commitment_id', 'fk_checklist_feas_id')
                ->references('id')->on('team_feasibility_commitments')
                ->cascadeOnDelete();

            $table->foreign('parent_item_id', 'fk_checklist_parent_id')
                ->references('id')->on('team_feasibility_checklist_items')
                ->cascadeOnDelete();

            // Indexes
            $table->index('feasibility_commitment_id', 'idx_feas_comm_id');
            $table->index('parent_item_id', 'idx_parent_id');
            $table->index(['feasibility_commitment_id', 'order_sequence'], 'idx_feas_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_feasibility_checklist_items');
    }
};
