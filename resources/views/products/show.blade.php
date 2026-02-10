@extends('layouts.app')

@section('title', 'View Product')

@section('content')
<h1>Product Details</h1>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ $product->name }}</h5>
        <h6 class="card-subtitle mb-2 text-muted">Price: ${{ number_format($product->price, 2) }}</h6>
        <p class="card-text">{{ $product->description }}</p>
        <p class="card-text">
            <small class="text-muted">
                Created: {{ $product->created_at->format('M d, Y') }}
                <br>
                Updated: {{ $product->updated_at->format('M d, Y') }}
            </small>
        </p>
        <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Back to List</a>
    </div>
</div>
@endsection