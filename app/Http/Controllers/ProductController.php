<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;


class ProductController extends Controller
{


    // Search + Pagination

    public function index(Request $request)
    {

        $products = Product::query();


        // Search by name

        if ($request->search) {
            $products->where(
                'name',
                'LIKE',
                '%' . $request->search . '%'
            );
        }



        // Category Filter

        if ($request->category) {
            $products->where(
                'category',
                $request->category
            );
        }




        // Status Filter

        if ($request->status) {
            $products->where(
                'status',
                $request->status
            );
        }





        // Minimum Price

        if ($request->min_price) {
            $products->where(
                'price',
                '>=',
                $request->min_price
            );
        }




        // Maximum Price

        if ($request->max_price) {
            $products->where(
                'price',
                '<=',
                $request->max_price
            );
        }




        $products = $products
            ->oldest()
            ->paginate(5)
            ->withQueryString();



        return view(
            'products.index',
            compact('products')
        );
    }




    public function create()
    {
        return view('products.create');
    }





    public function store(Request $request)
    {


        $request->validate([

            'name' => 'required',
            'price' => 'required|integer',
            'description' => 'nullable',
            'status' => 'required'

        ]);



        Product::create(
            $request->all()
        );


        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Product Created Successfully'
            );
    }





    public function show(Product $product)
    {
        return view(
            'products.show',
            compact('product')
        );
    }





    public function edit(Product $product)
    {
        return view(
            'products.edit',
            compact('product')
        );
    }





    public function update(Request $request, Product $product)
    {

        $request->validate([

            'name' => 'required',
            'price' => 'required|integer',
            'status' => 'required'

        ]);


        $product->update(
            $request->all()
        );


        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Product Updated Successfully'
            );
    }





    public function destroy(Product $product)
    {

        $product->delete();


        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Product Deleted Successfully'
            );
    }


}
