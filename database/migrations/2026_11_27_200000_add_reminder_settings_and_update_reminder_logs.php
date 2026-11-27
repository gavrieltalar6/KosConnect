<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambahkan kolom status saklar aktif reminder pada tabel tenants
        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'is_reminder_active')) {
                $table->boolean('is_reminder_active')->default(true)->after('due_day');
            }
        });

        // 2. Tambahkan tenant_id pada reminder_logs agar riwayat pengingat bisa terlacak langsung per penghuni
        Schema::table('reminder_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('reminder_logs', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->nullable()->after('invoice_id');
                $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            }
        });

        // 3. Modifikasi kolom enum type pada reminder_logs agar mendukung H-7, H-1, Hari H, dan manual
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE reminder_logs MODIFY COLUMN type VARCHAR(30) NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (Schema::hasColumn('tenants', 'is_reminder_active')) {
                $table->dropColumn('is_reminder_active');
            }
        });

        Schema::table('reminder_logs', function (Blueprint $table) {
            if (Schema::hasColumn('reminder_logs', 'tenant_id')) {
                $table->dropForeign(['tenant_id']);
                $table->dropColumn('tenant_id');
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE reminder_logs MODIFY COLUMN type ENUM('h_minus_3', 'h_minus_1', 'manual') NOT NULL");
        }
    }
};
