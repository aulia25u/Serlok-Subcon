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
        Schema::table('master_items', function (Blueprint $table) {
            $table->unsignedBigInteger('master_customer_id')->nullable()->after('tenant_id');
            $table->string('product_status')->nullable()->after('item_code');
            $table->string('part_number')->nullable()->after('product_status');
            $table->string('model')->nullable()->after('part_number');

            $table->foreign('master_customer_id')->references('id')->on('master_customers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_items', function (Blueprint $table) {
            $table->dropForeign(['master_customer_id']);
            $table->dropColumn(['master_customer_id', 'product_status', 'part_number', 'model']);
        });
    }
};
