<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Buat tabel log
        Schema::create('procurement_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_request_id')->constrained()->onDelete('cascade');
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->foreignId('user_id')->constrained();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Trigger: Auto log saat status berubah
        DB::unprepared('
            CREATE TRIGGER trg_procurement_status_change
            AFTER UPDATE ON procurement_requests
            FOR EACH ROW
            BEGIN
                IF OLD.status != NEW.status THEN
                    INSERT INTO procurement_logs (procurement_request_id, old_status, new_status, user_id, created_at, updated_at)
                    VALUES (NEW.id, OLD.status, NEW.status, NEW.user_id, NOW(), NOW());
                END IF;
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_procurement_status_change');
        Schema::dropIfExists('procurement_logs');
    }
};