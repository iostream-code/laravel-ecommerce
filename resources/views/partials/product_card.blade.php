<a href="{{ route('product', $product) }}" class="card card-produk text-decoration-none h-100 overflow-hidden">
    <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->name }}">
    <div class="card-body">
        @if ($product->category)
            <span class="badge badge-kategori mb-2">{{ $product->category->name }}</span>
        @endif
        <h3 class="h6 fw-bold text-dark mb-1 text-truncate">{{ $product->name }}</h3>
        <p class="fw-bolder text-tk mb-1">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
        <small class="text-muted">
            @if ($product->stock > 0)
                Stok: {{ $product->stock }}
            @else
                <span class="text-danger fw-bold">Stok habis</span>
            @endif
        </small>
    </div>
</a>
