@extends('layouts.app')

@section('title', 'Checkout — ' . config('app.name'))

@section('content')
<div class="container" style="max-width: 860px">
    <h1 class="h3 fw-bold mb-4"><i class="bi bi-bag-check me-1"></i>Checkout</h1>

    <div class="row g-4">
        <div class="col-md-7">
            <form method="POST" action="{{ route('checkout') }}" class="card p-4">
                @csrf
                <h2 class="h6 fw-bold text-uppercase text-muted mb-3">Alamat Pengiriman</h2>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Penerima</label>
                    <input type="text" name="recipient_name" value="{{ old('recipient_name', Auth::user()->name) }}"
                        class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">No. HP</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control"
                        placeholder="08xxxxxxxxxx" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Alamat Lengkap</label>
                    <textarea name="address" rows="3" class="form-control" required
                        placeholder="Jalan, nomor rumah, kecamatan, kota, kode pos">{{ old('address') }}</textarea>
                </div>
                <button class="btn btn-tk btn-lg">Buat Pesanan</button>
            </form>
        </div>

        <div class="col-md-5">
            <div class="card p-4">
                <h2 class="h6 fw-bold text-uppercase text-muted mb-3">Ringkasan Pesanan</h2>
                @foreach ($carts as $cart)
                    <div class="d-flex justify-content-between small mb-2">
                        <span>{{ $cart->product->name }} × {{ $cart->amount }}</span>
                        <span class="fw-semibold">Rp{{ number_format($cart->amount * $cart->product->price, 0, ',', '.') }}</span>
                    </div>
                @endforeach
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Total</span>
                    <span class="h5 fw-bolder text-tk">Rp{{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
