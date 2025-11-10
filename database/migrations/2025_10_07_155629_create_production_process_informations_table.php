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
        Schema::create('production_process_informations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_information_id')->constrained()->onDelete('cascade');
            $table->enum('process_location', ['in_house', 'out_house'])->default('in_house');
            $table->string('supplier_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_process_informations');
    }
};
