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
        Schema::create('receivings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_item_id')->constrained('master_items')->onDelete('cascade');
            $table->string('doc_number_internal')->nullable();
            $table->string('qrcode_customer')->nullable();
            $table->string('doc_number_customer')->nullable();
            $table->string('product_status')->default('Waiting'); // Waiting, Verified
            $table->date('delivery_date_customer')->nullable();
            $table->dateTime('incoming_date')->useCurrent();
            $table->foreignId('receive_by')->constrained('users');
            $table->double('qty_pack')->default(0);
            $table->double('qty_per_pack')->default(0);
            $table->string('delivery_by')->nullable();
            $table->string('ng_customer')->default('OK'); // OK, NG
            $table->foreignId('ng_operator')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receivings');
    }
};
