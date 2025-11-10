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
        Schema::create('sales_informations', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('part_no')->nullable();
            $table->string('part_name')->nullable();
            $table->date('date_masspro')->nullable();
            $table->integer('qty_month')->nullable();
            $table->string('depreciation_periode')->nullable();
            $table->string('tools_depreciation')->nullable();
            $table->boolean('critical_safety')->default(false);
            $table->boolean('regular_part')->default(false);
            $table->json('model')->nullable();
            $table->string('waya_ply_value')->nullable();
            $table->string('wrapping_ply_value')->nullable();
            $table->string('model_other_value')->nullable();
            $table->text('note')->nullable();
            $table->enum('decision', ['ok', 'no'])->nullable();
            $table->string('approved_by')->nullable();
            $table->string('checked_by_1')->nullable();
            $table->string('checked_by_2')->nullable();
            $table->string('prepared_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_informations');
    }
};
