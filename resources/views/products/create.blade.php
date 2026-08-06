@extends('layouts.app')

@section('title', 'Add New Product')

@section('content')
<h1>Add New Product</h1>

<form action="{{ route('products.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label for="name" class="form-label">Product Name</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>

    <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" class="form-control" id="price" name="price" required min="0">
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Category</label>

        <select name="category" class="form-select" required>

            <option value="">Select Category</option>

            <option value="Electronics">Electronics</option>

            <option value="Clothing">Clothing</option>

            <option value="Books">Books</option>

            <option value="Sports">Sports</option>

            <option value="Home & Kitchen">Home & Kitchen</option>

            <option value="Beauty">Beauty</option>

            <option value="Toys">Toys</option>

            <option value="Furniture">Furniture</option>

        </select>
    </div>


    <div class="mb-3">

        <label>Status</label>

        <select name="status" class="form-control">

            <option value="active">
                Active
            </option>

            <option value="inactive">
                Inactive
            </option>


        </select>

    </div>

    <button type="submit" class="btn btn-primary">Save Product</button>
    <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection