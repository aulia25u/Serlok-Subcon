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
            ->where('menu_name', 'Stock Opname Process')
            ->update(['menu_name' => 'SO Process', 'updated_at' => now()]);

        DB::table('menus')
            ->where('menu_name', 'Stock Opname Adjustment')
            ->update(['menu_name' => 'SO Adjustment', 'updated_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('menus')
            ->where('menu_name', 'SO Process')
            ->update(['menu_name' => 'Stock Opname Process', 'updated_at' => now()]);

        DB::table('menus')
            ->where('menu_name', 'SO Adjustment')
            ->update(['menu_name' => 'Stock Opname Adjustment', 'updated_at' => now()]);
    }
};
