@extends('layouts.app')

@section('title', "Pesanan #{$order->id} — " . config('app.name'))

@section('content')
<div class="container" style="max-width: 860px">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0">Pesanan #{{ $order->id }}</h1>
        <span class="badge fs-6 text-bg-{{ $order->status_color }}">{{ $order->status_label }}</span>
    </div>

    <div class="row g-4">
        <div class="col-md-7">
            <div class="card p-4 mb-4">
                <h2 class="h6 fw-bold text-uppercase text-muted mb-3">Rincian Item</h2>
                @foreach ($order->transactions as $trx)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="fw-semibold">{{ $trx->product?->name ?? 'Produk terhapus' }}</span>
                            <small class="text-muted d-block">
                                {{ $trx->amount }} × Rp{{ number_format($trx->price, 0, ',', '.') }}
                            </small>
                        </div>
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
                <h2 class="h6 fw-bold text-uppercase text-muted mb-3">Alamat Pengiriman</h2>
                <p class="mb-0 fw-semibold">{{ $order->recipient_name }} · {{ $order->phone }}</p>
                <p class="text-muted mb-0">{{ $order->address }}</p>
            </div>
        </div>

        <div class="col-md-5">
            @if ($order->status === 'menunggu_pembayaran')
                <div class="card p-4">
                    <h2 class="h6 fw-bold text-uppercase text-muted mb-3">Pembayaran</h2>

                    @if ($snapToken)
                        {{-- Midtrans Snap --}}
                        <button id="btn-bayar" class="btn btn-tk-accent btn-lg w-100">
                            <i class="bi bi-credit-card me-1"></i>Bayar Sekarang
                        </button>
                        @push('scripts')
                            <script src="https://app.{{ config('midtrans.is_production') ? '' : 'sandbox.' }}midtrans.com/snap/snap.js"
                                data-client-key="{{ config('midtrans.client_key') }}"></script>
                            <script>
                                document.getElementById('btn-bayar').addEventListener('click', function () {
                                    snap.pay(@json($snapToken), {
                                        onSuccess: () => location.reload(),
                                        onPending: () => location.reload(),
                                    });
                                });
                            </script>
                        @endpush
                    @else
                        {{-- Transfer manual --}}
                        <p class="small text-muted">
                            Transfer ke rekening <strong>BCA 1234567890 a.n. TokoKita</strong>
                            sebesar <strong>Rp{{ number_format($order->total, 0, ',', '.') }}</strong>,
                            lalu unggah bukti pembayaran di bawah ini.
                        </p>
                        <form method="POST" action="{{ route('submit_payment', $order) }}" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="payment_receipt" accept="image/*" class="form-control mb-3" required>
                            <button class="btn btn-tk w-100"><i class="bi bi-upload me-1"></i>Kirim Bukti Bayar</button>
                        </form>
                    @endif
                </div>
            @elseif ($order->status === 'menunggu_verifikasi')
                <div class="card p-4 text-center">
                    <i class="bi bi-hourglass-split fs-1 text-warning"></i>
                    <p class="fw-semibold mt-2 mb-0">Bukti pembayaran sedang diverifikasi admin.</p>
                </div>
            @else
                <div class="card p-4 text-center">
                    <i class="bi bi-patch-check fs-1 text-success"></i>
                    <p class="fw-semibold mt-2 mb-0">Pembayaran selesai. Terima kasih!</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
