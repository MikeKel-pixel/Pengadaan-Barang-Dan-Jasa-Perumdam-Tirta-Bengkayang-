<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambahkan unique constraint pada email suppliers (jika belum)
        Schema::table('suppliers', function (Blueprint $table) {
            // Pastikan email unique
            $table->unique('email', 'suppliers_email_unique');
        });

        // 2. Tambahkan constraint untuk memastikan status hanya 3 nilai
        DB::statement("ALTER TABLE suppliers ADD CONSTRAINT chk_suppliers_status CHECK (status IN ('pending', 'verified', 'rejected'))");
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropUnique('suppliers_email_unique');
        });
        DB::statement("ALTER TABLE suppliers DROP CONSTRAINT chk_suppliers_status");
    }
};