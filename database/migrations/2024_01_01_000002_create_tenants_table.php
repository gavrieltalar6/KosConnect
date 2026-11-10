<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('room_id')->nullable();
            $table->string('full_name');
            $table->string('phone');
            $table->string('origin_address');
            $table->string('guardian_phone');
            $table->unsignedBigInteger('telegram_chat_id')->nullable();
            $table->string('telegram_link_token')->unique()->nullable();
            $table->date('check_in_date');
            $table->tinyInteger('due_day');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};