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
        // Check if exists first to avoid duplicate if it was already there
        if (!DB::table('menus')->where('menu_name', 'Inventory Capture')->exists()) {
            DB::table('menus')->insert([
                'menu_name' => 'Inventory Capture',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('menus')->where('menu_name', 'Inventory Capture')->delete();
    }
};
