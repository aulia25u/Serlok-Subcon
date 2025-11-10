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
        Schema::create('tooling_inspection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tooling_inspection_id')->constrained('tooling_inspections')->onDelete('cascade');
            $table->integer('row_number')->default(0); // Nomor urut row
            $table->string('inspection_item')->nullable(); // DIAMETER 1, LENGTH, dll
            $table->string('inspection_method')->nullable(); // CALIPER, RULLER, VISUAL
            $table->string('standard')->nullable(); // Ø 101 ± 0,8, 250 ±, dll

            // 10 kolom Number of Tooling
            $table->string('tooling_1')->nullable();
            $table->string('tooling_2')->nullable();
            $table->string('tooling_3')->nullable();
            $table->string('tooling_4')->nullable();
            $table->string('tooling_5')->nullable();
            $table->string('tooling_6')->nullable();
            $table->string('tooling_7')->nullable();
            $table->string('tooling_8')->nullable();
            $table->string('tooling_9')->nullable();
            $table->string('tooling_10')->nullable();

            // X̄ dan R
            $table->string('x_bar')->nullable();
            $table->string('r_value')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tooling_inspection_items');
    }
};
