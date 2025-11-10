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
        Schema::create('material_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_information_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('material_id')->nullable();
            $table->string('specification')->nullable();
            $table->enum('new_material', ['yes', 'no'])->default('no');
            $table->string('code')->nullable();
            $table->decimal('thick', 10, 2)->nullable();
            $table->decimal('diameter_in', 10, 2)->nullable();
            $table->decimal('diameter_out', 10, 2)->nullable();
            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('volume', 15, 2)->nullable();
            $table->decimal('weight_estimate', 10, 2)->nullable();
            $table->decimal('weight_actual', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_calculations');
    }
};
