<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('midtrans_order_id')->nullable()->after('paid_at');
            $table->string('snap_token')->nullable()->after('midtrans_order_id');
            $table->timestamp('snap_token_created_at')->nullable()->after('snap_token');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('midtrans_order_id')->nullable()->after('note');
            // unique: satu transaksi Midtrans hanya boleh tercatat sekali (idempoten terhadap notifikasi ganda)
            $table->string('midtrans_transaction_id')->nullable()->unique()->after('midtrans_order_id');
            $table->string('payment_type')->nullable()->after('midtrans_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['midtrans_transaction_id']);
            $table->dropColumn(['midtrans_order_id', 'midtrans_transaction_id', 'payment_type']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['midtrans_order_id', 'snap_token', 'snap_token_created_at']);
        });
    }
};
