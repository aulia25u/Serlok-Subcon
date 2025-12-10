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
        Schema::create('surat_jalans', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique();
            $table->dateTime('surat_jalan_date');
            $table->string('status')->default('Draft');
            $table->timestamps();
        });

        // Add 'Surat Jalan' to menus table
        DB::table('menus')->insert([
            'menu_name' => 'Surat Jalan',
            'route_name' => 'rbac.surat-jalan', // Prefix for all resource routes
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_jalans');
        DB::table('menus')->where('menu_name', 'Surat Jalan')->delete();
    }
};
