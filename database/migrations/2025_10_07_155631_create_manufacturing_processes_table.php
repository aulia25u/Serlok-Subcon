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
        Schema::create('manufacturing_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_information_id')->constrained()->onDelete('cascade');
            $table->string('process_name');
            $table->boolean('enabled')->default(false);
            $table->unsignedBigInteger('machine_id')->nullable();
            $table->decimal('cycle_time_estimate', 10, 2)->nullable();
            $table->decimal('cycle_time_actual', 10, 2)->nullable();
            $table->decimal('capacity_estimate', 10, 2)->nullable();
            $table->decimal('capacity_actual', 10, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_processes');
    }
};
