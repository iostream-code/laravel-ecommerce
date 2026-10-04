@extends('layouts.app')

@section('title', 'Katalog — ' . config('app.name'))

@section('content')
<div class="container">
    <h1 class="h3 fw-bold mb-4">Katalog Produk</h1>

    {{-- Pencarian & filter --}}
    <form method="GET" action="{{ route('products') }}" class="card p-3 mb-4">
        <div class="row g-2">
            <div class="col-md-6">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                    placeholder="Cari produk...">
            </div>
            <div class="col-md-4">
                <select name="kategori" class="form-select">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $kategori)
                        <option value="{{ $kategori->slug }}" @selected(request('kategori') === $kategori->slug)>
                            {{ $kategori->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-tk"><i class="bi bi-search me-1"></i>Cari</button>
            </div>
        </div>
    </form>

    @if ($products->isEmpty())
        <div class="card p-5 text-center text-muted">
            <i class="bi bi-inbox fs-1"></i>
            <p class="mb-0 mt-2">Tidak ada produk yang cocok.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach ($products as $product)
                <div class="col-6 col-md-3">
                    @include('partials.product_card')
                </div>
            @endforeach
        </div>
        <div class="mt-4">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
