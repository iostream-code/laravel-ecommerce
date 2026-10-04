<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class OrderController extends Controller
{
    public function index(Request $req)
    {
        $orders = Order::with('user')->withCount('transactions')
            ->when($req->filled('status'), fn($q) => $q->where('status', $req->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('transactions.product', 'user');
        return view('admin.order_detail', compact('order'));
    }

    /** Verifikasi pembayaran manual / ubah status pesanan. */
    public function updateStatus(Request $req, Order $order)
    {
        $req->validate(['status' => 'required|in:' . implode(',', array_keys(Order::STATUS))]);

        $order->update([
            'status' => $req->status,
            'is_paid' => in_array($req->status, ['dibayar', 'dikirim', 'selesai'], true),
        ]);

        return Redirect::back()->with('success', 'Status pesanan diperbarui.');
    }
}
