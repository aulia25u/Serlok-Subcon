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
        // Add new menus
        $menus = [
            ['menu_name' => 'Stock Opname Process', 'created_at' => now(), 'updated_at' => now()],
            ['menu_name' => 'Stock Opname Adjustment', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('menus')->insert($menus);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('menus')->whereIn('menu_name', ['Stock Opname Process', 'Stock Opname Adjustment'])->delete();
    }
};
