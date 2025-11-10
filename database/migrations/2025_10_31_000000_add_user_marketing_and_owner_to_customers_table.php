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
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('user_marketing_id')->nullable()->constrained('users')->onDelete('set null')->after('status');
            $table->string('owner')->nullable()->after('user_marketing_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['user_marketing_id']);
            $table->dropColumn(['user_marketing_id', 'owner']);
        });
    }
};
