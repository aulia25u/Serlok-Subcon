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
        Schema::create('invoice_edit_history', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice_id')->comment('ID from dt_invoice table');
            $table->string('invoice_number', 100)->comment('Nomor Invoice');
            $table->string('field_name', 50)->comment('Field yang diedit: Jumlah, Satuan, Harga_Satuan');
            $table->text('old_value')->nullable()->comment('Nilai sebelum edit');
            $table->text('new_value')->nullable()->comment('Nilai setelah edit');
            $table->decimal('old_total', 15, 2)->nullable()->comment('Total Per Item sebelum edit');
            $table->decimal('new_total', 15, 2)->nullable()->comment('Total Per Item setelah edit');
            $table->unsignedBigInteger('edited_by')->comment('User ID yang melakukan edit');
            $table->string('editor_name', 100)->comment('Nama user yang melakukan edit');
            $table->timestamp('edited_at')->useCurrent()->comment('Waktu edit');
            $table->string('ip_address', 45)->nullable()->comment('IP Address user');
            $table->text('user_agent')->nullable()->comment('Browser/Device info');
            
            $table->index('invoice_id');
            $table->index('edited_by');
            $table->index('edited_at');
            
            $table->foreign('edited_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_edit_history');
    }
};
