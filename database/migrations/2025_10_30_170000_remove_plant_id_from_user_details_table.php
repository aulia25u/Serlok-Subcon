<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hanya lanjut jika kolom ada
        if (Schema::hasColumn('user_details', 'plant_id')) {
            // Cek apakah constraint foreign key-nya ada
            $foreignKeyName = 'user_details_plant_id_foreign';
            $constraintExists = DB::table('information_schema.table_constraints')
                ->where('constraint_schema', DB::getDatabaseName())
                ->where('table_name', 'user_details')
                ->where('constraint_name', $foreignKeyName)
                ->exists();

            Schema::table('user_details', function (Blueprint $table) use ($constraintExists) {
                if ($constraintExists) {
                    $table->dropForeign(['plant_id']);
                }
                $table->dropColumn('plant_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->foreignId('plant_id')
                ->nullable()
                ->constrained('plants')
                ->onDelete('set null');
        });
    }
};

