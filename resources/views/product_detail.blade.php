@extends('layouts.app')

@section('title', $product->name . ' — ' . config('app.name'))

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('products') }}" class="text-tk">Katalog</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-4 mb-5">
        <div class="col-md-5">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="img-fluid rounded-4 w-100"
                style="aspect-ratio: 4/3; object-fit: cover">
        </div>
        <div class="col-md-7">
            @if ($product->category)
                <span class="badge badge-kategori mb-2">{{ $product->category->name }}</span>
            @endif
            <h1 class="h3 fw-bolder">{{ $product->name }}</h1>
            <p class="display-6 fw-bolder text-tk">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
            <p class="text-muted">{{ $product->description }}</p>
            <p class="fw-semibold">
                Stok:
                @if ($product->stock > 0)
                    <span class="text-success">{{ $product->stock }} tersedia</span>
                @else
                    <span class="text-danger">Habis</span>
                @endif
            </p>

            @auth
                @unless(Auth::user()->is_admin)
                    @if ($product->stock > 0)
                        <form method="POST" action="{{ route('add_to_cart', $product) }}" class="d-flex gap-2" style="max-width: 320px">
                            @csrf
                            <input type="number" name="amount" value="1" min="1" max="{{ $product->stock }}"
                                class="form-control" style="width: 100px">
                            <button class="btn btn-tk flex-grow-1">
                                <i class="bi bi-cart-plus me-1"></i>Tambah ke Keranjang
                            </button>
                        </form>
                    @endif
                @endunless
            @else
                <a href="{{ route('login') }}" class="btn btn-tk">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Masuk untuk membeli
                </a>
            @endauth
        </div>
    </div>

    @if ($related->isNotEmpty())
        <h2 class="h5 fw-bold mb-3">Produk Serupa</h2>
        <div class="row g-4">
            @foreach ($related as $product)
                <div class="col-6 col-md-3">
                    @include('partials.product_card')
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
