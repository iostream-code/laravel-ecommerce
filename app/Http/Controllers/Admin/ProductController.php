<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Support\Webp;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $req)
    {
        $products = Product::with('category')
            ->when($req->filled('q'), fn($q) => $q->where('name', 'like', '%' . $req->q . '%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.products', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.product_form', ['product' => new Product(), 'categories' => $categories]);
    }

    public function store(Request $req)
    {
        $data = $this->validasi($req, wajibGambar: true);
        $data['image'] = Webp::simpan($req->file('image'), 'produk');
        Product::create($data);

        return Redirect::route('admin.products')->with('success', 'Produk ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.product_form', compact('product', 'categories'));
    }

    public function update(Request $req, Product $product)
    {
        $data = $this->validasi($req, wajibGambar: false);
        if ($req->hasFile('image')) {
            if ($product->image && !str_starts_with($product->image, 'http')) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = Webp::simpan($req->file('image'), 'produk');
        }
        $product->update($data);

        return Redirect::route('admin.products')->with('success', 'Produk diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->image && !str_starts_with($product->image, 'http')) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return Redirect::back()->with('success', 'Produk dihapus.');
    }

    private function validasi(Request $req, bool $wajibGambar): array
    {
        return $req->validate([
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'required|string',
            'image' => ($wajibGambar ? 'required' : 'nullable') . '|image|max:4096',
        ]);
    }
}
