<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cleaning_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('room_id')->nullable();
            $table->date('requested_date');
            $table->time('requested_time');
            $table->enum('presence_status', ['occupied', 'vacant']);
            $table->enum('status', ['pending', 'approved', 'rejected', 'rescheduled', 'done'])->default('pending');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->date('rescheduled_date')->nullable();
            $table->time('rescheduled_time')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('set null');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cleaning_schedules');
    }
};