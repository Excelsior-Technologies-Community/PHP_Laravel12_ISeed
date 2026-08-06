@extends('layouts.app')

@section('title', 'Products Management')

@section('content')

<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-1">
                <i class="bi bi-box-seam"></i> Product Management
            </h2>
            <p class="text-muted mb-0">
                Manage products, search, filter and export data.
            </p>
        </div>

        <div>

            <a href="{{ route('products.create') }}"
                class="btn btn-primary ms-2">
                <i class="bi bi-plus-circle"></i> Add Product
            </a>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">🔍 Search & Filters</h5>
        </div>

        <div class="card-body">

            <form method="GET">

                <div class="row g-3">

                    <div class="col-md-3">
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search Product..."
                            value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2">
                        <select name="category" class="form-select">

                            <option value="">All Categories</option>

                            <option value="Electronics"
                                {{ request('category') == 'Electronics' ? 'selected' : '' }}>
                                Electronics
                            </option>

                            <option value="Clothing"
                                {{ request('category') == 'Clothing' ? 'selected' : '' }}>
                                Clothing
                            </option>

                            <option value="Books"
                                {{ request('category') == 'Books' ? 'selected' : '' }}>
                                Books
                            </option>

                            <option value="Sports"
                                {{ request('category') == 'Sports' ? 'selected' : '' }}>
                                Sports
                            </option>

                            <option value="Home & Kitchen"
                                {{ request('category') == 'Home & Kitchen' ? 'selected' : '' }}>
                                Home & Kitchen
                            </option>

                            <option value="Beauty"
                                {{ request('category') == 'Beauty' ? 'selected' : '' }}>
                                Beauty
                            </option>

                            <option value="Toys"
                                {{ request('category') == 'Toys' ? 'selected' : '' }}>
                                Toys
                            </option>

                            <option value="Furniture"
                                {{ request('category') == 'Furniture' ? 'selected' : '' }}>
                                Furniture
                            </option>

                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="status" class="form-select">

                            <option value="">All Status</option>

                            <option value="active"
                                {{ request('status')=='active'?'selected':'' }}>
                                Active
                            </option>

                            <option value="inactive"
                                {{ request('status')=='inactive'?'selected':'' }}>
                                Inactive
                            </option>

                        </select>
                    </div>

                    <div class="col-md-2">
                        <input
                            type="number"
                            name="min_price"
                            class="form-control"
                            placeholder="Min Price"
                            value="{{ request('min_price') }}">
                    </div>

                    <div class="col-md-2">
                        <input
                            type="number"
                            name="max_price"
                            class="form-control"
                            placeholder="Max Price"
                            value="{{ request('max_price') }}">
                    </div>

                    <div class="col-md-1 d-grid">
                        <button class="btn btn-primary">
                            Search
                        </button>
                    </div>

                </div>

                <div class="mt-3">

                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">

                        Reset Filters

                    </a>

                </div>

            </form>

        </div>

    </div>

    {{-- Success Message --}}
    @if(session('success'))

    <div class="alert alert-success alert-dismissible fade show">

        {{ session('success') }}

        <button class="btn-close" data-bs-dismiss="alert"></button>

    </div>

    @endif

    {{-- Table --}}
    <div class="card shadow-sm border-0">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">

                Product List

            </h5>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th width="170">Actions</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($products as $product)

                        <tr>

                            <td>{{ $product->id }}</td>

                            <td class="fw-semibold">

                                {{ $product->name }}

                            </td>

                            <td>

                                <span class="badge bg-info text-dark">

                                    {{ $product->category }}

                                </span>

                            </td>

                            <td>

                                ₹{{ number_format($product->price) }}

                            </td>

                            <td>

                                @if($product->status=='active')

                                <span class="badge bg-success">

                                    Active

                                </span>

                                @else

                                <span class="badge bg-danger">

                                    Inactive

                                </span>

                                @endif

                            </td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">

                                    <a href="{{ route('products.show', $product) }}"
                                        class="btn btn-sm btn-info rounded-pill px-3"
                                        title="View">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>

                                    <a href="{{ route('products.edit', $product) }}"
                                        class="btn btn-sm btn-warning rounded-pill px-3"
                                        title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <form action="{{ route('products.destroy', $product) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this product?')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="btn btn-sm btn-danger rounded-pill px-3"
                                            title="Delete">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>

                                    </form>

                                </div>
                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="6" class="text-center py-5">

                                <h5 class="text-muted">

                                    No Products Found

                                </h5>

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        <div class="card-footer bg-white">

            <nav class="mt-3">
                <ul class="pagination justify-content-center">

                    @for($i = 1; $i <= $products->lastPage(); $i++)

                        <li class="page-item {{ $products->currentPage() == $i ? 'active' : '' }}">

                            <a class="page-link"
                                href="{{ $products->url($i) }}">

                                {{ $i }}

                            </a>

                        </li>

                        @endfor

                </ul>
            </nav>

        </div>

    </div>

</div>

@endsection