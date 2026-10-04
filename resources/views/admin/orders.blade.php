@extends('layouts.app')

@section('title', 'Kelola Pesanan — ' . config('app.name'))

@section('content')
<div class="container">
    <h1 class="h3 fw-bold mb-4"><i class="bi bi-receipt me-1"></i>Kelola Pesanan</h1>

    <div class="mb-3 d-flex flex-wrap gap-2">
        <a href="{{ route('admin.orders') }}"
            class="btn btn-sm {{ request('status') ? 'btn-outline-secondary' : 'btn-tk' }}">Semua</a>
        @foreach (\App\Models\Order::STATUS as $kode => $info)
            <a href="{{ route('admin.orders', ['status' => $kode]) }}"
                class="btn btn-sm {{ request('status') === $kode ? 'btn-tk' : 'btn-outline-secondary' }}">
                {{ $info['label'] }}
            </a>
        @endforeach
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light"><tr class="small text-uppercase text-muted">
                    <th class="ps-3">#</th><th>Pelanggan</th><th>Tanggal</th><th>Item</th><th>Total</th><th>Metode</th><th class="pe-3">Status</th>
                </tr></thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td class="ps-3">
                                <a href="{{ route('admin.orders.show', $order) }}" class="fw-bold text-tk text-decoration-none">
                                    #{{ $order->id }}
                                </a>
                            </td>
                            <td>{{ $order->user->name }}</td>
                            <td><small>{{ $order->created_at->translatedFormat('d M Y H:i') }}</small></td>
                            <td>{{ $order->transactions_count }}</td>
                            <td>Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                            <td><small class="text-uppercase fw-semibold">{{ $order->payment_method }}</small></td>
                            <td class="pe-3"><span class="badge text-bg-{{ $order->status_color }}">{{ $order->status_label }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $orders->links('pagination::bootstrap-5') }}</div>
</div>
@endsection
