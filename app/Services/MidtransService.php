<?php

namespace App\Services;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class MidtransService
{
    /** Midtrans aktif hanya bila key sudah diisi di .env. */
    public static function aktif(): bool
    {
        return config('midtrans.server_key') !== '' && config('midtrans.client_key') !== '';
    }

    private static function setup(): void
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /** Buat Snap token untuk sebuah order. */
    public static function buatSnapToken(Order $order): string
    {
        self::setup();

        $items = $order->transactions->map(fn($t) => [
            'id' => (string) $t->product_id,
            'price' => $t->price,
            'quantity' => $t->amount,
            'name' => mb_substr($t->product->name, 0, 50),
        ])->values()->all();

        return Snap::getSnapToken([
            'transaction_details' => [
                'order_id' => 'ORDER-' . $order->id . '-' . time(),
                'gross_amount' => $order->total,
            ],
            'item_details' => $items,
            'customer_details' => [
                'first_name' => $order->recipient_name ?? $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->phone,
            ],
        ]);
    }

    /**
     * Proses notifikasi webhook Midtrans.
     * Mengembalikan order yang diperbarui, atau null bila tidak dikenal.
     */
    public static function prosesNotifikasi(): ?Order
    {
        self::setup();
        $notif = new Notification();

        // order_id format: ORDER-{id}-{timestamp}
        if (!preg_match('/^ORDER-(\d+)-\d+$/', $notif->order_id ?? '', $m)) {
            return null;
        }
        $order = Order::find((int) $m[1]);
        if (!$order) {
            return null;
        }

        $status = $notif->transaction_status;
        $fraud = $notif->fraud_status ?? 'accept';

        if (in_array($status, ['capture', 'settlement'], true) && $fraud === 'accept') {
            $order->update(['status' => 'dibayar', 'is_paid' => true]);
        } elseif (in_array($status, ['deny', 'cancel', 'expire'], true)) {
            $order->update(['status' => 'dibatalkan']);
        }
        // 'pending' dibiarkan menunggu_pembayaran

        return $order;
    }
}
