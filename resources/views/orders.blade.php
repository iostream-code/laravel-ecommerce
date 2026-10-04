@extends('layouts.app')

@section('title', 'Pesanan Saya — ' . config('app.name'))

@section('content')
<div class="container" style="max-width: 860px">
    <h1 class="h3 fw-bold mb-4"><i class="bi bi-receipt me-1"></i>Pesanan Saya</h1>

    @if ($orders->isEmpty())
        <div class="card p-5 text-center text-muted">
            <i class="bi bi-receipt-cutoff fs-1"></i>
            <p class="mt-2">Belum ada pesanan.</p>
            <a href="{{ route('products') }}" class="btn btn-tk mx-auto">Mulai Belanja</a>
        </div>
    @else
        @foreach ($orders as $order)
            <a href="{{ route('detail_order', $order) }}" class="card p-3 mb-3 text-decoration-none card-produk">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <span class="fw-bold text-dark">Pesanan #{{ $order->id }}</span>
                        <small class="text-muted d-block">
                            {{ $order->created_at->translatedFormat('d M Y H:i') }} · {{ $order->transactions_count }} item
                        </small>
                    </div>
                    <div class="text-end">
                        <span class="badge text-bg-{{ $order->status_color }}">{{ $order->status_label }}</span>
                        <p class="fw-bolder text-tk mb-0">Rp{{ number_format($order->total, 0, ',', '.') }}</p>
                    </div>
                </div>
            </a>
        @endforeach
        {{ $orders->links('pagination::bootstrap-5') }}
    @endif
</div>
@endsection
