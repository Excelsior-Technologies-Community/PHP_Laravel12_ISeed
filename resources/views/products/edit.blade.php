@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<h1>Edit Product</h1>

<form action="{{ route('products.update', $product) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="name" class="form-label">Product Name</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ $product->name }}" required>
    </div>

    <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" class="form-control" id="price" name="price" value="{{ $product->price }}" required min="0">
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" rows="3">{{ $product->description }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Category</label>

        <select name="category" class="form-select" required>

            <option value="Electronics"
                {{ $product->category == 'Electronics' ? 'selected' : '' }}>
                Electronics
            </option>

            <option value="Clothing"
                {{ $product->category == 'Clothing' ? 'selected' : '' }}>
                Clothing
            </option>

            <option value="Books"
                {{ $product->category == 'Books' ? 'selected' : '' }}>
                Books
            </option>

            <option value="Sports"
                {{ $product->category == 'Sports' ? 'selected' : '' }}>
                Sports
            </option>

            <option value="Home & Kitchen"
                {{ $product->category == 'Home & Kitchen' ? 'selected' : '' }}>
                Home & Kitchen
            </option>

            <option value="Beauty"
                {{ $product->category == 'Beauty' ? 'selected' : '' }}>
                Beauty
            </option>

            <option value="Toys"
                {{ $product->category == 'Toys' ? 'selected' : '' }}>
                Toys
            </option>

            <option value="Furniture"
                {{ $product->category == 'Furniture' ? 'selected' : '' }}>
                Furniture
            </option>

        </select>
    </div>

    <div class="mb-3">

        <label>Status</label>


        <select name="status" class="form-control">


            <option value="active"
                @if($product->status=='active')
                selected
                @endif
                >
                Active
            </option>



            <option value="inactive"
                @if($product->status=='inactive')
                selected
                @endif
                >
                Inactive
            </option>


        </select>


    </div>

    <button type="submit" class="btn btn-primary">Update Product</button>
    <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection