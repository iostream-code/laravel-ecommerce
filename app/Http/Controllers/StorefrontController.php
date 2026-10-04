<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function landing()
    {
        $featured = Product::with('category')->where('stock', '>', 0)->latest()->take(8)->get();
        $categories = Category::withCount('products')->get();

        return view('landing', compact('featured', 'categories'));
    }

    public function catalog(Request $req)
    {
        $categories = Category::orderBy('name')->get();

        $products = Product::with('category')
            ->when($req->filled('q'), fn($q) =>
                $q->where(fn($w) => $w
                    ->where('name', 'like', '%' . $req->q . '%')
                    ->orWhere('description', 'like', '%' . $req->q . '%')))
            ->when($req->filled('kategori'), fn($q) =>
                $q->whereHas('category', fn($c) => $c->where('slug', $req->kategori)))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('products', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        $related = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('stock', '>', 0)
            ->take(4)->get();

        return view('product_detail', compact('product', 'related'));
    }
}
