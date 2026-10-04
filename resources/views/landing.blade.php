@extends('layouts.app')

@section('title', config('app.name') . ' — Belanja Mudah & Terpercaya')

@section('content')
<div class="container">
    {{-- Hero --}}
    <section class="hero-tk text-white p-5 mb-5">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bolder mb-3">Belanja kebutuhanmu,<br>semudah satu klik.</h1>
                <p class="lead opacity-75 mb-4">Produk pilihan dengan harga bersahabat — elektronik, fashion, kuliner, hingga perlengkapan rumah.</p>
                <a href="{{ route('products') }}" class="btn btn-tk-accent btn-lg px-4">
                    <i class="bi bi-bag me-1"></i>Jelajahi Katalog
                </a>
            </div>
            <div class="col-md-4 text-center d-none d-md-block">
                <i class="bi bi-bag-heart" style="font-size: 9rem; opacity: .25"></i>
            </div>
        </div>
    </section>

    {{-- Kategori --}}
    <section class="mb-5">
        <h2 class="h4 fw-bold mb-3">Kategori</h2>
        <div class="row g-3">
            @foreach ($categories as $kategori)
                <div class="col-6 col-md-3">
                    <a href="{{ route('products', ['kategori' => $kategori->slug]) }}"
                        class="card card-produk text-decoration-none text-center p-4 h-100">
                        <i class="bi bi-tag fs-2 text-tk"></i>
                        <span class="fw-bold mt-2 text-dark">{{ $kategori->name }}</span>
                        <small class="text-muted">{{ $kategori->products_count }} produk</small>
                    </a>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Produk terbaru --}}
    <section>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 fw-bold mb-0">Produk Terbaru</h2>
            <a href="{{ route('products') }}" class="text-tk fw-semibold text-decoration-none">
                Lihat semua <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="row g-4">
            @foreach ($featured as $product)
                <div class="col-6 col-md-3">
                    @include('partials.product_card')
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
