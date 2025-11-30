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
        // Drop the existing foreign key first
        Schema::table('master_customers', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
        });

        // Update existing data: set tenant_id to customer_id from tenant_owners.customer_id
        \DB::statement('
            UPDATE master_customers
            SET tenant_id = (
                SELECT tenant_owners.customer_id
                FROM tenant_owners
                WHERE tenant_owners.id = master_customers.tenant_id
            )
            WHERE tenant_id IS NOT NULL
        ');

        Schema::table('master_customers', function (Blueprint $table) {
            $table->renameColumn('tenant_id', 'customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_customers', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->renameColumn('customer_id', 'tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenant_owners')->nullOnDelete();
        });

        // Note: Reversing data is complex, as multiple tenant_owners can have same customer_id
        // This down migration assumes no data reversal for simplicity
    }
};
