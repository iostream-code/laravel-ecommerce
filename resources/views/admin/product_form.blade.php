@extends('layouts.app')

@section('title', ($product->exists ? 'Ubah' : 'Tambah') . ' Produk — ' . config('app.name'))

@section('content')
<div class="container" style="max-width: 720px">
    <h1 class="h3 fw-bold mb-4">{{ $product->exists ? 'Ubah' : 'Tambah' }} Produk</h1>

    <form method="POST" enctype="multipart/form-data" class="card p-4"
        action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
        @csrf
        @if ($product->exists) @method('PATCH') @endif

        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Produk</label>
            <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control" required>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Kategori</label>
                <select name="category_id" class="form-select" required>
                    <option value="">— pilih —</option>
                    @foreach ($categories as $kategori)
                        <option value="{{ $kategori->id }}" @selected(old('category_id', $product->category_id) == $kategori->id)>
                            {{ $kategori->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Harga (Rp)</label>
                <input type="number" name="price" value="{{ old('price', $product->price) }}" min="0" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Stok</label>
                <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" class="form-control" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea name="description" rows="4" class="form-control" required>{{ old('description', $product->description) }}</textarea>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold">Gambar {{ $product->exists ? '(kosongkan jika tetap)' : '' }}</label>
            <input type="file" name="image" accept="image/*" class="form-control" {{ $product->exists ? '' : 'required' }}>
            @if ($product->exists)
                <img src="{{ $product->image_url }}" class="rounded-3 mt-2" width="120" alt="">
            @endif
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.products') }}" class="btn btn-outline-secondary flex-grow-1">Batal</a>
            <button class="btn btn-tk flex-grow-1">Simpan</button>
        </div>
    </form>
</div>
@endsection
