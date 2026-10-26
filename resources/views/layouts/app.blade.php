<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laravel iSeed Studio')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css">

    <style>
        body {
            background-color: #f8fafc;
            color: #1e293b;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .card {
            border: 1px solid rgba(0,0,0,.08);
            box-shadow: 0 1px 3px 0 rgba(0,0,0,.05);
            border-radius: 0.75rem;
        }
        .card-header {
            font-weight: 600;
            border-top-left-radius: 0.75rem !important;
            border-top-right-radius: 0.75rem !important;
        }
        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
        }
        .badge {
            font-weight: 600;
            border-radius: 0.375rem;
        }
        pre[class*="language-"] {
            border-radius: 0.5rem;
            margin: 0;
            max-height: 500px;
        }
        .diff-added {
            background-color: rgba(46, 160, 67, 0.2);
            color: #2ea043;
            display: block;
            padding: 2px 4px;
        }
        .diff-removed {
            background-color: rgba(248, 81, 73, 0.2);
            color: #f85149;
            display: block;
            padding: 2px 4px;
        }
        .diff-line-num {
            color: #64748b;
            user-select: none;
            width: 45px;
            display: inline-block;
            text-align: right;
            margin-right: 12px;
        }
    </style>
    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-2">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('seeder.dashboard') }}">
                <span class="fs-4">🌱</span>
                <span>Laravel <span class="text-success">iSeed</span> Studio</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('seeder.dashboard') ? 'active text-white fw-bold' : '' }}" 
                           href="{{ route('seeder.dashboard') }}">
                            <i class="bi bi-cpu me-1"></i> Seeder Studio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') ? 'active text-white fw-bold' : '' }}" 
                           href="{{ route('products.index') }}">
                            <i class="bi bi-boxes me-1"></i> Products Data
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('iseed-demo') ? 'active text-white fw-bold' : '' }}" 
                           href="/iseed-demo">
                            <i class="bi bi-journal-code me-1"></i> Documentation
                        </a>
                    </li>
                </ul>

                <span class="badge bg-secondary-subtle text-secondary border px-3 py-2">
                    Laravel 12.x • iSeed 3.7
                </span>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 mt-4 mb-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5 align-middle"></i>
                <strong>Success:</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5 align-middle"></i>
                <strong>Notice:</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-markup-templating.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js"></script>
    @stack('scripts')
</body>

</html>
