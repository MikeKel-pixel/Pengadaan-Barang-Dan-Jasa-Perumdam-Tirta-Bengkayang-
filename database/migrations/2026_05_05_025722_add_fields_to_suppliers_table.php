<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('npwp')->nullable()->after('pic');
            $table->string('bidang_usaha')->nullable()->after('npwp');
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending')->after('bidang_usaha');
            $table->datetime('registered_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['npwp', 'bidang_usaha', 'status', 'registered_at']);
        });
    }
};