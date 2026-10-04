@extends('layouts.app')

@section('title', "Pesanan #{$order->id} — Admin")

@section('content')
<div class="container" style="max-width: 920px">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.orders') }}" class="text-tk">Kelola Pesanan</a></li>
            <li class="breadcrumb-item active">#{{ $order->id }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0">Pesanan #{{ $order->id }}</h1>
        <span class="badge fs-6 text-bg-{{ $order->status_color }}">{{ $order->status_label }}</span>
    </div>

    <div class="row g-4">
        <div class="col-md-7">
            <div class="card p-4 mb-4">
                <h2 class="h6 fw-bold text-uppercase text-muted mb-3">Item</h2>
                @foreach ($order->transactions as $trx)
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ $trx->product?->name ?? 'Produk terhapus' }} × {{ $trx->amount }}</span>
                        <span class="fw-bold">Rp{{ number_format($trx->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Total</span>
                    <span class="h5 fw-bolder text-tk mb-0">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="card p-4">
                <h2 class="h6 fw-bold text-uppercase text-muted mb-3">Pelanggan & Pengiriman</h2>
                <p class="mb-1"><strong>{{ $order->user->name }}</strong> ({{ $order->user->email }})</p>
                <p class="mb-1">{{ $order->recipient_name }} · {{ $order->phone }}</p>
                <p class="text-muted mb-0">{{ $order->address }}</p>
            </div>
        </div>

        <div class="col-md-5">
            @if ($order->payment_receipt)
                <div class="card p-4 mb-4">
                    <h2 class="h6 fw-bold text-uppercase text-muted mb-3">Bukti Pembayaran</h2>
                    <a href="{{ asset('storage/' . $order->payment_receipt) }}" target="_blank">
                        <img src="{{ asset('storage/' . $order->payment_receipt) }}" class="img-fluid rounded-3" alt="Bukti bayar">
                    </a>
                </div>
            @endif

            <div class="card p-4">
                <h2 class="h6 fw-bold text-uppercase text-muted mb-3">Ubah Status</h2>
                <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                    @csrf @method('PATCH')
                    <select name="status" class="form-select mb-3">
                        @foreach (\App\Models\Order::STATUS as $kode => $info)
                            <option value="{{ $kode }}" @selected($order->status === $kode)>{{ $info['label'] }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-tk w-100">Simpan Status</button>
                </form>
                @if ($order->status === 'menunggu_verifikasi')
                    <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="mt-2">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="dibayar">
                        <button class="btn btn-success w-100"><i class="bi bi-check2-circle me-1"></i>Terima Pembayaran</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
