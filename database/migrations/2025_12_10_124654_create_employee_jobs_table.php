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
        Schema::create('employee_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outgoing_id')->constrained('outgoings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Assigned To
            $table->dateTime('created_datetime'); // From outgoing
            $table->dateTime('start_datetime')->nullable();
            $table->dateTime('finished_datetime')->nullable();
            $table->integer('qty_ok')->default(0);
            $table->integer('qty_ng')->default(0);
            $table->integer('qty_ng_customer')->default(0);
            $table->foreignId('inspector_id')->constrained('users')->onDelete('cascade');
            $table->string('surat_jalan_status')->nullable();
            $table->timestamps();
        });

        DB::table('menus')->insert([
            'menu_name' => 'Employee Jobs',
            'route_name' => 'rbac.employee-jobs.index',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('menus')->where('menu_name', 'Employee Jobs')->delete();
        Schema::dropIfExists('employee_jobs');
    }
};