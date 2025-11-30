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
        // Set invalid tenant_id to null
        \DB::table('master_customers')
            ->whereNotNull('tenant_id')
            ->whereNotExists(function ($query) {
                $query->select(\DB::raw(1))
                      ->from('tenant_owners')
                      ->whereRaw('tenant_owners.id = master_customers.tenant_id');
            })
            ->update(['tenant_id' => null]);

        Schema::table('master_customers', function (Blueprint $table) {
            $table->foreign('tenant_id')->references('id')->on('tenant_owners')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_customers', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
        });
    }
};
