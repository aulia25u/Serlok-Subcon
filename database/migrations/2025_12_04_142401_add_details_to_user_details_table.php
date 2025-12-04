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
        Schema::table('user_details', function (Blueprint $table) {
            $table->string('nip')->nullable()->after('employee_id');
            $table->string('employee_status')->nullable()->after('nip'); // Tetap, Kontrak, Borongan
            $table->text('blacklist_note')->nullable()->after('employee_status');
            $table->string('bank_name')->nullable()->after('blacklist_note');
            $table->string('bank_account_name')->nullable()->after('bank_name');
            $table->string('bank_account_number')->nullable()->after('bank_account_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->dropColumn([
                'nip',
                'employee_status',
                'blacklist_note',
                'bank_name',
                'bank_account_name',
                'bank_account_number'
            ]);
        });
    }
};
