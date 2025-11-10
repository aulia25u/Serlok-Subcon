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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('position_id');
            $table->foreignId('role_id');
            $table->foreignId('plant_id');
            $table->string('employee_id');
            $table->string('employee_name');
            $table->string('gender');
            $table->string('address');
            $table->string('phone');
            $table->timestamp('join_date');
            $table->boolean('status_active');
            $table->text('employee_photo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shimada_user_details');
    }
};
