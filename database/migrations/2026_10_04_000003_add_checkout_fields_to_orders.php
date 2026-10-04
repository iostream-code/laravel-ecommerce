<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->default('menunggu_pembayaran')->after('id');
            $table->unsignedBigInteger('total')->default(0)->after('status');
            $table->string('recipient_name')->nullable()->after('total');
            $table->string('phone')->nullable()->after('recipient_name');
            $table->text('address')->nullable()->after('phone');
            $table->string('payment_method')->default('manual')->after('address');
            $table->string('snap_token')->nullable()->after('payment_method');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('price')->default(0)->after('amount')
                ->comment('harga satuan saat pembelian');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['status', 'total', 'recipient_name', 'phone', 'address', 'payment_method', 'snap_token']);
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
