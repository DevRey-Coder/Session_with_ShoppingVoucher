<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::when(request()->has('keyword'),function($query){
            $query->where(function(Builder $builder){
                $keyword = request()->keyword;
                $builder->where('name','LIKE','%'.$keyword.'&');
            });
        })->latest('id')->paginate(10)->withQueryString();

        return response()->json([
            'status' => true,
            'data' => ProductResource::collection($products)
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|string',
            'amount' => 'required|numeric',
            'stock' => 'required|min:10|max:100'
        ]);

        $product = Product::create([
            'name' => $request->name,
            'amount' => $request->amount,
            'stock' => $request->stock
        ]);

        return response()->json([
            'status' => true,
            'data' => new ProductResource($product)
        ]);
    }
}
