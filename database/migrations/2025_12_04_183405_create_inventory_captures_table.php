<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_captures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_item_id')->constrained('master_items')->onDelete('cascade');
            $table->double('quantity');
            $table->date('captured_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_captures');
    }
};
