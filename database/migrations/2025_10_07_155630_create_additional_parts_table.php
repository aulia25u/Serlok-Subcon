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
        Schema::create('additional_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_information_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('part_id')->nullable();
            $table->string('part_name')->nullable();
            $table->string('specification')->nullable();
            $table->integer('qty_unit')->nullable();
            $table->string('supplier')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('additional_parts');
    }
};
