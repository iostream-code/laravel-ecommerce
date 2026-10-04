@extends('layouts.app')

@section('title', 'Kelola Produk — ' . config('app.name'))

@section('content')
<div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h1 class="h3 fw-bold mb-0"><i class="bi bi-box-seam me-1"></i>Kelola Produk</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-tk"><i class="bi bi-plus-lg me-1"></i>Tambah Produk</a>
    </div>

    <form method="GET" class="mb-3" style="max-width: 360px">
        <div class="input-group">
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari produk...">
            <button class="btn btn-outline-success"><i class="bi bi-search"></i></button>
        </div>
    </form>

    <div class="card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light"><tr class="small text-uppercase text-muted">
                    <th class="ps-3">Produk</th><th>Kategori</th><th>Harga</th><th>Stok</th><th class="text-end pe-3">Aksi</th>
                </tr></thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $product->image_url }}" width="44" height="44" class="rounded-3"
                                        style="object-fit: cover" alt="">
                                    <span class="fw-semibold">{{ $product->name }}</span>
                                </div>
                            </td>
                            <td>{{ $product->category?->name ?? '—' }}</td>
                            <td>Rp{{ number_format($product->price, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge text-bg-{{ $product->stock === 0 ? 'danger' : ($product->stock <= 5 ? 'warning' : 'success') }}">
                                    {{ $product->stock }}
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline"
                                    onsubmit="return confirm('Hapus produk {{ $product->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $products->links('pagination::bootstrap-5') }}</div>
</div>
@endsection
