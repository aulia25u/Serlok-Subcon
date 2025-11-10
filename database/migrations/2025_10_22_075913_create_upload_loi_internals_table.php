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
        Schema::create('upload_loi_internals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('part_id'); // This matches the bigint type of parts.id
            $table->text('image');
            $table->timestamps();
            
            // $table->foreign('part_id')
            //       ->references('id')
            //       ->on('parts')
            //       ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upload_loi_internals');
    }
};