<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->text('route_name')->nullable()->after('menu_name');
        });

        // Populate existing menus
        $mappings = [
            'User Management' => 'rbac.user-data',
            'Company Management' => 'rbac.company,rbac.role,rbac.department,rbac.section,rbac.position,rbac.plant',
            'History Management' => 'rbac.history',
            'Menu Management' => 'rbac.master-menu',
            'Tenant List Management' => 'rbac.customer',
            'Tenant Owner Management' => 'rbac.tenant-owner',
            'Master Customer' => 'rbac.master-customer',
            'Master Item' => 'rbac.master-item',
        ];

        foreach ($mappings as $menuName => $routeName) {
            DB::table('menus')->where('menu_name', $menuName)->update(['route_name' => $routeName]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('route_name');
        });
    }
};
