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
        Schema::table('invoice_edit_history', function (Blueprint $table) {
            $table->string('process_id', 50)->nullable()->after('invoice_number')->comment('Process ID from dt_invoice');
            $table->string('telegram_chat_id', 50)->nullable()->after('process_id')->comment('Telegram Chat ID from dt_invoice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_edit_history', function (Blueprint $table) {
            $table->dropColumn(['process_id', 'telegram_chat_id']);
        });
    }
};
