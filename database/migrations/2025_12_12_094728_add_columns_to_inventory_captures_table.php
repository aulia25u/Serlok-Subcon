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
        Schema::table('inventory_captures', function (Blueprint $table) {
            $table->double('physical_quantity')->nullable()->after('quantity');
            $table->boolean('is_adjusted')->default(false)->after('physical_quantity');
            $table->dateTime('adjusted_at')->nullable()->after('is_adjusted');
            $table->text('notes')->nullable()->after('adjusted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_captures', function (Blueprint $table) {
            $table->dropColumn(['physical_quantity', 'is_adjusted', 'adjusted_at', 'notes']);
        });
    }
};
