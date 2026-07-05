<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan foreign key menggunakan cascade
        Schema::table('procurement_details', function (Blueprint $table) {
            // Drop existing foreign key jika ada
            $table->dropForeign(['procurement_request_id']);
            $table->dropForeign(['item_id']);
            
            // Tambahkan kembali dengan cascade
            $table->foreign('procurement_request_id')
                  ->references('id')
                  ->on('procurement_requests')
                  ->onDelete('cascade');
                  
            $table->foreign('item_id')
                  ->references('id')
                  ->on('items')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('procurement_details', function (Blueprint $table) {
            $table->dropForeign(['procurement_request_id']);
            $table->dropForeign(['item_id']);
            
            $table->foreign('procurement_request_id')
                  ->references('id')
                  ->on('procurement_requests');
                  
            $table->foreign('item_id')
                  ->references('id')
                  ->on('items');
        });
    }
};