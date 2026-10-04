<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'produk' => Product::count(),
            'pesanan_baru' => Order::whereIn('status', ['menunggu_pembayaran', 'menunggu_verifikasi'])->count(),
            'pendapatan' => Order::whereIn('status', ['dibayar', 'dikirim', 'selesai'])->sum('total'),
            'pelanggan' => User::where('is_admin', false)->count(),
        ];
        $pesananTerbaru = Order::with('user')->latest()->take(8)->get();
        $stokMenipis = Product::where('stock', '<=', 5)->orderBy('stock')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'pesananTerbaru', 'stokMenipis'));
    }
}
