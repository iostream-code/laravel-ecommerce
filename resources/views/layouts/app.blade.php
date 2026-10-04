<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'TokoKita'))</title>

    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,600,700,800" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --tk-primary: #047857;
            --tk-primary-dark: #065f46;
            --tk-accent: #f59e0b;
            --tk-bg: #f8faf9;
        }
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; background: var(--tk-bg); }
        .btn-tk { background: var(--tk-primary); color: #fff; font-weight: 700; }
        .btn-tk:hover { background: var(--tk-primary-dark); color: #fff; }
        .btn-tk-accent { background: var(--tk-accent); color: #fff; font-weight: 700; }
        .btn-tk-accent:hover { background: #d97706; color: #fff; }
        .text-tk { color: var(--tk-primary) !important; }
        .bg-tk { background: var(--tk-primary) !important; }
        .navbar-tk { background: #fff; border-bottom: 1px solid #e7ece9; }
        .card { border: 1px solid #e7ece9; border-radius: 1rem; }
        .card-produk { transition: transform .15s ease, box-shadow .15s ease; }
        .card-produk:hover { transform: translateY(-4px); box-shadow: 0 .75rem 1.5rem rgba(4,120,87,.12); }
        .card-produk img { aspect-ratio: 4/3; object-fit: cover; }
        .badge-kategori { background: #d1fae5; color: var(--tk-primary-dark); font-weight: 700; }
        .hero-tk { background: linear-gradient(135deg, var(--tk-primary-dark), var(--tk-primary)); border-radius: 1.5rem; }
        footer { border-top: 1px solid #e7ece9; }
    </style>
    @stack('head')
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-tk sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bolder text-tk" href="{{ route('landing') }}">
                <i class="bi bi-bag-heart-fill me-1"></i>{{ config('app.name', 'TokoKita') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navTk">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navTk">
                <ul class="navbar-nav me-auto">
                    @if (auth()->user()?->is_admin)
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.products') }}">Produk</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.orders') }}">Pesanan</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('landing') }}">Beranda</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('products') }}">Katalog</a></li>
                    @endif
                </ul>

                <ul class="navbar-nav ms-auto align-items-md-center">
                    @auth
                        @unless(Auth::user()->is_admin)
                            <li class="nav-item me-md-2">
                                <a class="nav-link position-relative" href="{{ route('cart') }}" title="Keranjang">
                                    <i class="bi bi-cart3 fs-5"></i>
                                    @php($jumlahKeranjang = \App\Models\Cart::where('user_id', Auth::id())->count())
                                    @if ($jumlahKeranjang)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $jumlahKeranjang }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                        @endunless
                    @endauth

                    @guest
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Masuk</a></li>
                        <li class="nav-item ms-md-2">
                            <a class="btn btn-tk btn-sm px-3" href="{{ route('register') }}">Daftar</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-semibold" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if (Auth::user()->is_admin)
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i>Dashboard Admin</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @else
                                    <li><a class="dropdown-item" href="{{ route('orders') }}">
                                        <i class="bi bi-receipt me-2"></i>Pesanan Saya</a></li>
                                @endif
                                <li><a class="dropdown-item" href="{{ route('profile') }}">
                                    <i class="bi bi-person me-2"></i>Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right me-2"></i>Keluar
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    <footer class="bg-white py-4 mt-5">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <span class="fw-bold text-tk"><i class="bi bi-bag-heart-fill me-1"></i>{{ config('app.name', 'TokoKita') }}</span>
            <small class="text-muted">© {{ date('Y') }} — Dibangun dengan Laravel 12 & Bootstrap 5</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
