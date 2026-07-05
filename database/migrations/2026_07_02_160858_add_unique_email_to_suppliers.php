<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // CEK DULU apakah kolom email ada dan belum unique
        if (Schema::hasTable('suppliers')) {
            // Cek apakah constraint sudah ada
            $hasUnique = false;
            $columns = Schema::getColumnListing('suppliers');
            if (in_array('email', $columns)) {
                Schema::table('suppliers', function (Blueprint $table) {
                    // Tambahkan unique constraint
                    $table->unique('email', 'suppliers_email_unique');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropUnique('suppliers_email_unique');
        });
    }
};