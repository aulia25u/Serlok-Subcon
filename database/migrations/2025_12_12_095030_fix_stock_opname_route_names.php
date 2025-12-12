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
        DB::table('menus')
            ->where('menu_name', 'SO Process')
            ->update([
                    'route_name' => 'rbac.stock-opname',
                    'updated_at' => now()
                ]);

        DB::table('menus')
            ->where('menu_name', 'SO Adjustment')
            ->update([
                    'route_name' => 'rbac.stock-adjustment',
                    'updated_at' => now()
                ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to revert specifically for a fix, can leave as is or set to null
        DB::table('menus')
            ->whereIn('menu_name', ['SO Process', 'SO Adjustment'])
            ->update(['route_name' => null]);
    }
};
