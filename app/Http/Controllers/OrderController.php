<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Form alamat & ringkasan sebelum membuat pesanan. */
    public function checkoutForm()
    {
        $carts = Cart::with('product')->where('user_id', Auth::id())->get();
        if ($carts->isEmpty()) {
            return Redirect::route('cart')->with('error', 'Keranjang masih kosong.');
        }
        $total = $carts->sum(fn($c) => $c->amount * $c->product->price);

        return view('checkout', compact('carts', 'total'));
    }

    /** Buat pesanan: kurangi stok secara atomik, simpan total & alamat. */
    public function checkout(Request $req)
    {
        $data = $req->validate([
            'recipient_name' => 'required|string|max:100',
            'phone' => 'required|string|max:30',
            'address' => 'required|string|max:1000',
        ]);

        $order = DB::transaction(function () use ($data) {
            $carts = Cart::with('product')
                ->where('user_id', Auth::id())
                ->lockForUpdate()
                ->get();

            if ($carts->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'Keranjang kosong.']);
            }

            $total = 0;
            foreach ($carts as $cart) {
                $product = Product::whereKey($cart->product_id)->lockForUpdate()->first();
                if ($product->stock < $cart->amount) {
                    throw ValidationException::withMessages([
                        'cart' => "Stok {$product->name} tersisa {$product->stock}.",
                    ]);
                }
                $total += $cart->amount * $product->price;
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'status' => 'menunggu_pembayaran',
                'total' => $total,
                'recipient_name' => $data['recipient_name'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'payment_method' => MidtransService::aktif() ? 'midtrans' : 'manual',
            ]);

            foreach ($carts as $cart) {
                $cart->product->decrement('stock', $cart->amount);
                Transaction::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'amount' => $cart->amount,
                    'price' => $cart->product->price,
                ]);
                $cart->delete();
            }

            return $order;
        });

        return Redirect::route('detail_order', $order)
            ->with('success', 'Pesanan dibuat. Silakan lakukan pembayaran.');
    }

    /** Daftar pesanan MILIK user yang login saja. */
    public function orders()
    {
        $orders = Order::withCount('transactions')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('orders', compact('orders'));
    }

    public function detailOrder(Order $order)
    {
        abort_unless($order->user_id === Auth::id() || Auth::user()->is_admin, 403);
        $order->load('transactions.product');

        // Siapkan Snap token bila metode midtrans & belum dibayar
        $snapToken = null;
        if ($order->payment_method === 'midtrans'
            && $order->status === 'menunggu_pembayaran'
            && MidtransService::aktif()) {
            $snapToken = $order->snap_token ?: MidtransService::buatSnapToken($order);
            $order->update(['snap_token' => $snapToken]);
        }

        return view('order_detail', compact('order', 'snapToken'));
    }

    /** Pembayaran manual: upload bukti transfer. */
    public function submitPayment(Request $req, Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);
        abort_unless($order->status === 'menunggu_pembayaran', 422, 'Pesanan ini tidak menunggu pembayaran.');

        $req->validate([
            'payment_receipt' => 'required|image|max:4096',
        ]);

        // Disimpan di disk privat; diakses lewat route ber-otorisasi,
        // sehingga tidak butuh storage:link dan tidak terbuka untuk publik.
        $path = $req->file('payment_receipt')->store('bukti-bayar');
        $order->update([
            'payment_receipt' => $path,
            'status' => 'menunggu_verifikasi',
        ]);

        return Redirect::back()->with('success', 'Bukti pembayaran terkirim, menunggu verifikasi admin.');
    }

    /** Tampilkan bukti pembayaran (pemilik pesanan atau admin saja). */
    public function buktiBayar(Order $order)
    {
        abort_unless($order->user_id === Auth::id() || Auth::user()->is_admin, 403);
        abort_unless($order->payment_receipt, 404);

        // Dukung file lama yang tersimpan di disk public
        foreach (['local', 'public'] as $disk) {
            if (\Illuminate\Support\Facades\Storage::disk($disk)->exists($order->payment_receipt)) {
                return \Illuminate\Support\Facades\Storage::disk($disk)->response($order->payment_receipt);
            }
        }
        abort(404);
    }

    /** Webhook notifikasi Midtrans (tanpa CSRF). */
    public function midtransCallback()
    {
        if (!MidtransService::aktif()) {
            abort(404);
        }
        MidtransService::prosesNotifikasi();
        return response()->json(['ok' => true]);
    }
}
