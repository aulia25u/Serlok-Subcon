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
        $parentId = \Illuminate\Support\Facades\DB::table('menus')->where('menu_name', 'Master Data')->value('id');

        if ($parentId) {
            \Illuminate\Support\Facades\DB::table('menus')->insert([
                'menu_name' => 'Master Finance',
                'route_name' => 'rbac.master-finance',
                'parent_id' => $parentId,
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
        \Illuminate\Support\Facades\DB::table('menus')->where('menu_name', 'Master Finance')->delete();
    }
};
