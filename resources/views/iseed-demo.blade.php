@extends('layouts.app')

@section('title', 'iSeed Demo')

@section('content')
<h1>Laravel iSeed Package Demonstration</h1>

<div class="card mt-4">
    <div class="card-header bg-primary text-white">
        <h3>What is iSeed?</h3>
    </div>
    <div class="card-body">
        <p>iSeed is a Laravel package that allows you to generate seeders from existing database tables.</p>
        
        <h5>Benefits:</h5>
        <ul>
            <li>Generate seeders from existing data</li>
            <li>Perfect for transferring data between environments</li>
            <li>Easy backup of current database state</li>
            <li>Great for testing with real data</li>
        </ul>
        
        <h5>Commands Used in This Project:</h5>
        <div class="alert alert-secondary">
            <code>php artisan iseed products</code>
        </div>
        
        <h5>Generated Seeder File:</h5>
        <p>Check <code>database/seeders/ProductsTableSeeder.php</code> to see the auto-generated seeder with your product data.</p>
        
        <h5>Try It Yourself:</h5>
        <ol>
            <li>Add more products using the form</li>
            <li>Run <code>php artisan iseed products --force</code></li>
            <li>Check the updated seeder file</li>
        </ol>
        
        <div class="alert alert-info">
            <strong>Note:</strong> You can seed the database anytime using: 
            <code>php artisan db:seed</code>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-success text-white">
        <h3>Quick Links</h3>
    </div>
    <div class="card-body">
        <a href="{{ route('products.index') }}" class="btn btn-primary">View Products</a>
        <a href="{{ route('products.create') }}" class="btn btn-success">Add New Product</a>
    </div>
</div>
@endsection