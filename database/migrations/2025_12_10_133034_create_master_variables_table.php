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
        Schema::create('master_variables', function (Blueprint $table) {
            $table->id();
            $table->string('variable_code')->unique();
            $table->string('variable_name');
            $table->string('variable_value');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Add 'Master Variable' to menus table
        DB::table('menus')->insert([
            'menu_name' => 'Master Variable',
            'route_name' => 'rbac.master-variable', // Prefix for all resource routes
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Seed default variables
        DB::table('master_variables')->insert([
            [
                'variable_code' => 'DOC_NUM_FORMAT',
                'variable_name' => 'Document Number Format',
                'variable_value' => 'SJ/{Y}/{m}/{SEQ}',
                'description' => 'Format for Surat Jalan document number. {Y}=Year, {m}=Month, {SEQ}=Sequence',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'variable_code' => 'DATE_FORMAT',
                'variable_name' => 'Date Format',
                'variable_value' => 'YYYY-MM-DD HH:mm',
                'description' => 'Standard date format for the system',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_variables');
        DB::table('menus')->where('menu_name', 'Master Variable')->delete();
    }
};
