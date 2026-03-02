<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'customer_name' => 'required|string'
        ]);

        // 2. TAHAP INTI PENELITIAN: Mengambil harga asli dari database (Server-side)
        // lockForUpdate() = pessimistic locking untuk mencegah race condition
        // Baris produk akan ter-lock sampai transaction selesai
        // Jika 2 user request bersamaan, yang satunya akan menunggu lock release
        $product = Product::lockForUpdate()->find($request->product_id);

        if (!$product) {
            return response()->json([
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }
        
        // --- cek stok, supaya user tidak bisa memesan lebih banyak dari yang ada
        if ($request->quantity > $product->stock) {
            return response()->json([
                'message' => 'Stok tidak cukup'
            ], 422);
        }

        // Kalkulasi total harga dilakukan di Back-end
        $totalPrice = $product->price * $request->quantity;

        // 3. Simpan ke Tabel Orders (dalam transaction)
        $order = Order::create([
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'total_price' => $totalPrice, // Hasil kalkulasi server
            'customer_name' => $request->customer_name,
            'status' => 'pending'
        ]);

        // 4. Kurangi stok produk setelah pesanan berhasil dibuat
        // Karena pakai lockForUpdate(), update ini atomic dan aman dari race condition
        $product->decrement('stock', $request->quantity);

        return response()->json([
            'message' => 'Pesanan berhasil dibuat!',
            'data' => $order
        ], 201);
    }
}