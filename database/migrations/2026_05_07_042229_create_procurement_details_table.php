<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_request_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->foreignId('item_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->integer('jumlah');
            $table->decimal('harga_estimasi', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_details');
    }
};