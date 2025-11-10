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
        Schema::table('pos_queue', function (Blueprint $table) {
            $table->boolean('is_scheduled')->default(false)->after('status');
            $table->time('schedule_time')->nullable()->after('is_scheduled');
            $table->timestamp('last_run')->nullable()->after('schedule_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_queue', function (Blueprint $table) {
            $table->dropColumn(['is_scheduled', 'schedule_time', 'last_run']);
        });
    }
};
