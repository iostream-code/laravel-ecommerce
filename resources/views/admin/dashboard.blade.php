@extends('layouts.app')

@section('title', 'Dashboard Admin — ' . config('app.name'))

@section('content')
<div class="container">
    <h1 class="h3 fw-bold mb-4"><i class="bi bi-speedometer2 me-1"></i>Dashboard Admin</h1>

    <div class="row g-3 mb-4">
        @foreach ([
            ['Produk', $stats['produk'], 'bi-box-seam', 'primary', route('admin.products')],
            ['Pesanan Perlu Tindakan', $stats['pesanan_baru'], 'bi-bell', 'warning', route('admin.orders')],
            ['Pendapatan', 'Rp' . number_format($stats['pendapatan'], 0, ',', '.'), 'bi-cash-stack', 'success', route('admin.orders')],
            ['Pelanggan', $stats['pelanggan'], 'bi-people', 'info', '#'],
        ] as [$label, $nilai, $ikon, $warna, $url])
            <div class="col-6 col-lg-3">
                <a href="{{ $url }}" class="card p-4 text-decoration-none h-100">
                    <i class="bi {{ $ikon }} fs-3 text-{{ $warna }}"></i>
                    <p class="h4 fw-bolder text-dark mb-0 mt-2">{{ $nilai }}</p>
                    <small class="text-muted">{{ $label }}</small>
                </a>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card p-4">
                <h2 class="h6 fw-bold text-uppercase text-muted mb-3">Pesanan Terbaru</h2>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr class="text-muted small text-uppercase">
                            <th>#</th><th>Pelanggan</th><th>Total</th><th>Status</th>
                        </tr></thead>
                        <tbody>
                            @forelse ($pesananTerbaru as $order)
                                <tr>
                                    <td><a href="{{ route('admin.orders.show', $order) }}" class="fw-bold text-tk text-decoration-none">#{{ $order->id }}</a></td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                                    <td><span class="badge text-bg-{{ $order->status_color }}">{{ $order->status_label }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">Belum ada pesanan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card p-4">
                <h2 class="h6 fw-bold text-uppercase text-muted mb-3">⚠️ Stok Menipis (≤ 5)</h2>
                @forelse ($stokMenipis as $product)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-truncate me-2">{{ $product->name }}</span>
                        <span class="badge text-bg-{{ $product->stock === 0 ? 'danger' : 'warning' }}">{{ $product->stock }}</span>
                    </div>
                @empty
                    <p class="text-muted mb-0">Semua stok aman. 👍</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
