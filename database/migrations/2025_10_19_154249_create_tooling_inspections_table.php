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
        Schema::create('tooling_inspections', function (Blueprint $table) {
            $table->id();

            // Customer information
            $table->integer('customer_id')->nullable();
            $table->string('customer')->nullable();

            // Basic information
            $table->date('date');
            $table->string('part_no')->nullable();
            $table->integer('quantity');
            $table->string('image')->nullable(); // Link to uploaded drawing sketch

            // Result
            $table->enum('result', ['OK', 'NG'])->nullable();

            // Tooling type (SINGLE SELECTION dengan checkboxes di mockup)
            $table->string('tooling_type')->nullable(); // KIGATA, MANDREL, GONOGO, MALL CUTTING, MALL CHECKING

            // Note
            $table->text('note')->nullable();

            // Inspected and Approved by (User IDs)
            $table->foreignId('inspected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // Indexes
            $table->index('customer_id');
            $table->index('part_no');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tooling_inspections');
    }
};
