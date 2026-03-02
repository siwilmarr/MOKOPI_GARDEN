<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Tampilkan semua produk
     */
    public function index()
    {
        $products = Product::select('id', 'name', 'description', 'price', 'stock')->get();

        return response()->json([
            'message' => 'Daftar produk berhasil diambil',
            'data' => $products
        ], 200);
    }

    /**
     * Tampilkan detail satu produk
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'data' => $product
        ], 200);
    }
}
