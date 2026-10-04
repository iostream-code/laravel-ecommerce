@extends('layouts.app')

@section('title', 'Keranjang — ' . config('app.name'))

@section('content')
<div class="container" style="max-width: 860px">
    <h1 class="h3 fw-bold mb-4"><i class="bi bi-cart3 me-1"></i>Keranjang Belanja</h1>

    @if ($carts->isEmpty())
        <div class="card p-5 text-center text-muted">
            <i class="bi bi-cart-x fs-1"></i>
            <p class="mt-2">Keranjangmu masih kosong.</p>
            <a href="{{ route('products') }}" class="btn btn-tk mx-auto">Mulai Belanja</a>
        </div>
    @else
        @php($total = 0)
        @foreach ($carts as $cart)
            @php($total += $cart->amount * $cart->product->price)
            <div class="card p-3 mb-3">
                <div class="row g-3 align-items-center">
                    <div class="col-3 col-md-2">
                        <img src="{{ $cart->product->image_url }}" class="img-fluid rounded-3"
                            style="aspect-ratio: 1; object-fit: cover" alt="{{ $cart->product->name }}">
                    </div>
                    <div class="col-9 col-md-4">
                        <p class="fw-bold mb-0">{{ $cart->product->name }}</p>
                        <small class="text-muted">Rp{{ number_format($cart->product->price, 0, ',', '.') }} / pcs</small>
                    </div>
                    <div class="col-6 col-md-3">
                        <form method="POST" action="{{ route('update_cart', $cart) }}" class="d-flex gap-2">
                            @csrf @method('PATCH')
                            <input type="number" name="amount" value="{{ $cart->amount }}" min="1"
                                max="{{ $cart->product->stock }}" class="form-control form-control-sm" style="width: 80px">
                            <button class="btn btn-sm btn-outline-success" title="Perbarui jumlah">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                        </form>
                    </div>
                    <div class="col-4 col-md-2 fw-bolder text-tk">
                        Rp{{ number_format($cart->amount * $cart->product->price, 0, ',', '.') }}
                    </div>
                    <div class="col-2 col-md-1 text-end">
                        <form method="POST" action="{{ route('delete_cart', $cart) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="card p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="text-muted">Total</span>
                <p class="h4 fw-bolder text-tk mb-0">Rp{{ number_format($total, 0, ',', '.') }}</p>
            </div>
            <a href="{{ route('checkout_form') }}" class="btn btn-tk-accent btn-lg px-4">
                Lanjut ke Checkout <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    @endif
</div>
@endsection
