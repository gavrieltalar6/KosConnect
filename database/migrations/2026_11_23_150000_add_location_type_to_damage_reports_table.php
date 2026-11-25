<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom location_type dan shared_area_detail ke tabel damage_reports.
     * File ini dibuat terpisah agar bisa di-merge nanti tanpa mengubah migration lama.
     */
    public function up(): void
    {
        Schema::table('damage_reports', function (Blueprint $table) {
            // Jenis lokasi kerusakan: kamar sendiri atau ruangan bersama
            $table->string('location_type', 20)->default('own_room')->after('description');
            // Detail lokasi untuk ruangan bersama (nullable)
            $table->string('shared_area_detail', 255)->nullable()->after('location_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('damage_reports', function (Blueprint $table) {
            $table->dropColumn(['location_type', 'shared_area_detail']);
        });
    }
};
