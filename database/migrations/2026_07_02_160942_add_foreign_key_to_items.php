<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan foreign key di items ke categories
        if (Schema::hasTable('items')) {
            Schema::table('items', function (Blueprint $table) {
                // Cek apakah foreign key sudah ada
                // Hapus dulu jika ada (agar tidak error)
                $table->dropForeign(['category_id']);
                // Tambahkan kembali
                $table->foreign('category_id')
                      ->references('id')
                      ->on('categories')
                      ->onDelete('restrict'); // RESTRICT = tidak boleh hapus kategori yang dipakai
            });
        }
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });
    }
};