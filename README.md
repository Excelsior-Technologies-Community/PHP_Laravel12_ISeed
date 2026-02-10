# PHP_Laravel12_ISeed
## Overview

Laravel iSeed Project is a practical demonstration project built with Laravel 12 to understand how to generate database seeders from existing tables using the **iSeed package**.
The project also includes a complete Product CRUD module so developers can see how real data can be converted into reusable seeders.

This project is useful for learning database seeding, CRUD operations, and environment data transfer in Laravel applications.

---

## Key Concepts

* Laravel CRUD Operations
* Database Migrations
* Database Seeding
* iSeed Package Usage
* MVC Architecture
* Blade Views with Bootstrap

---

## Prerequisites

* PHP 8 or higher
* Composer
* MySQL
* Node.js (Optional)
* Laravel 12

---

## Installation Steps

### Step 1: Create Laravel Project

```bash
composer create-project laravel/laravel laravel-iseed-project
cd laravel-iseed-project
```

---

### Step 2: Configure Database

Edit `.env` file:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=iseed_db
DB_USERNAME=root
DB_PASSWORD=
```

Create database manually using phpMyAdmin or MySQL command:

```sql
CREATE DATABASE iseed_db;
```

---

### Step 3: Create Products Table

```bash
php artisan make:migration create_products_table
```

Run migration:

```bash
php artisan migrate
```

---

### Step 4: Insert Dummy Data

**Option A – Tinker**

```bash
php artisan tinker
```

Insert sample records using DB queries.

**Option B – Seeder**

```bash
php artisan make:seeder ProductsTableSeeder
php artisan db:seed --class=ProductsTableSeeder
```

---

### Step 5: Install iSeed Package

```bash
composer require orangehill/iseed
php artisan vendor:publish --provider="Orangehill\Iseed\IseedServiceProvider"
```

---

### Step 6: Generate Seeder from Existing Table

```bash
php artisan iseed products
```

Additional commands:

```bash
php artisan iseed products,users
php artisan iseed products --force
```

This creates a seeder file with existing database data.

---

### Step 7: Configure DatabaseSeeder

Update `database/seeders/DatabaseSeeder.php` to call generated seeders and run:

```bash
php artisan db:seed
```

---

### Step 8: Create Model and Controller

```bash
php artisan make:model Product -mcr
```

Configure `$fillable` fields in Product model.

---

### Step 9: Product CRUD Controller

Implement methods:

* index
* create
* store
* show
* edit
* update
* destroy

---

### Step 10: Routes Setup

Add resource routes in `routes/web.php`:

```
Route::resource('products', ProductController::class);
Route::get('/iseed-demo', function () {
    return view('iseed-demo');
});
```

---

### Step 11: Create Views

Create Blade files:

* layouts/app.blade.php
* products/index.blade.php
* products/create.blade.php
* products/show.blade.php
* products/edit.blade.php
* iseed-demo.blade.php

Use Bootstrap for styling and navigation.

---

### Step 12: Run the Project

```bash
php artisan serve
```

Open in browser:

```
http://127.0.0.1:8000
```

<img width="1783" height="892" alt="image" src="https://github.com/user-attachments/assets/82dc0461-7a87-4fa6-abda-2e1af586f995" />

<img width="1719" height="733" alt="image" src="https://github.com/user-attachments/assets/024ea25a-7333-4d9b-b022-5dc914932db5" />

<img width="1759" height="973" alt="image" src="https://github.com/user-attachments/assets/c7744e31-6a0c-4935-baa3-c368fbdd5b23" />


---

## Application URLs

* Products List: `/products`
* Add Product: `/products/create`
* iSeed Demo: `/iseed-demo`

---

## Optional Advanced Usage

### Generate Seeder for Users

```bash
php artisan iseed users
```

### Multiple Tables

```bash
php artisan iseed users,products
```

### Custom Configuration

Edit `config/iseed.php` to adjust chunk size, path, and exclusions.

### Advanced Commands

```bash
php artisan iseed products --classname=CustomSeeder
php artisan iseed products --orderby=price --direction=desc
php artisan iseed products --where="price > 1000"
```

---

## Project Structure

```
laravel-iseed-project/
app/
  Models/Product.php
  Http/Controllers/ProductController.php
database/
  migrations/
  seeders/
resources/
  views/layouts/
  views/products/
routes/web.php
.env
```

---

## Use Cases

* Database backup through seeders
* Environment data transfer
* Testing with real datasets
* Learning Laravel seeding
* Academic projects

---

## Best Practices

* Keep seeders version controlled
* Use chunking for large tables
* Exclude sensitive tables
* Validate input in CRUD forms

---

## Troubleshooting

* Ensure MySQL is running
* Check `.env` credentials
* Clear cache using `php artisan config:clear`
* Verify migration status with `php artisan migrate:status`

---

## License

MIT License
