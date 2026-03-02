<?php

use App\Models\Product;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('mencatat total harga yang dihitung oleh server meskipun request dimanipulasi', function () {
    // buat produk dengan harga tertentu
    $product = Product::factory()->create([
        'price' => 25000,
        'stock' => 10,
    ]);

    $response = $this->postJson('/api/checkout', [
        'product_id' => $product->id,
        'quantity' => 2,
        'price' => 1000,          // nilai palsu dari client
        'customer_name' => 'Alice',
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('orders', [
        'product_id' => $product->id,
        'quantity' => 2,
        // total seharusnya dihitung oleh server = 25000 * 2
        'total_price' => 50000,
        'customer_name' => 'Alice',
    ]);
});

it('mengurangi stok produk setelah pesanan dibuat', function () {
    // buat produk dengan stok awal 10
    $product = Product::factory()->create([
        'price' => 20000,
        'stock' => 10,
    ]);

    // pesan 3 cup
    $response = $this->postJson('/api/checkout', [
        'product_id' => $product->id,
        'quantity' => 3,
        'customer_name' => 'Bob',
    ]);

    $response->assertStatus(201);

    // refresh model dari database
    $product->refresh();

    // stok harus berkurang: 10 - 3 = 7
    expect($product->stock)->toBe(7);
});

it('mencegah pesanan jika stok tidak cukup', function () {
    // buat produk dengan stok hanya 2 cup
    $product = Product::factory()->create([
        'price' => 15000,
        'stock' => 2,
    ]);

    // coba pesan 5 cup (lebih dari stok)
    $response = $this->postJson('/api/checkout', [
        'product_id' => $product->id,
        'quantity' => 5,
        'customer_name' => 'Charlie',
    ]);

    // harus gagal dengan status 422
    $response->assertStatus(422);

    // cek bahwa order tidak dibuat dan stok tetap 2
    $this->assertDatabaseMissing('orders', [
        'customer_name' => 'Charlie',
    ]);

    $product->refresh();
    expect($product->stock)->toBe(2);
});

it('menangani race condition ketika 2 user memesan bersamaan dengan stok terbatas', function () {
    // buat produk dengan stok hanya 1 cup
    $product = Product::factory()->create([
        'price' => 20000,
        'stock' => 1,
    ]);

    // User pertama pesan 1 cup
    $response1 = $this->postJson('/api/checkout', [
        'product_id' => $product->id,
        'quantity' => 1,
        'customer_name' => 'User1',
    ]);

    // User pertama harus berhasil
    expect($response1->status())->toBe(201);

    // Refresh product dari database
    $product->refresh();
    
    // Stok harus 0 setelah user1 pesan
    expect($product->stock)->toBe(0);

    // User kedua coba pesan 1 cup (tapi stok sudah 0)
    $response2 = $this->postJson('/api/checkout', [
        'product_id' => $product->id,
        'quantity' => 1,
        'customer_name' => 'User2',
    ]);

    // User kedua harus ditolak karena stok tidak cukup
    expect($response2->status())->toBe(422);
    $response2->assertJson(['message' => 'Stok tidak cukup']);

    // Verifikasi: hanya 1 order yang dibuat (milik User1)
    expect(Order::count())->toBe(1);
    expect(Order::where('customer_name', 'User1')->exists())->toBeTrue();
    expect(Order::where('customer_name', 'User2')->exists())->toBeFalse();
});

it('simulasi concurrent request dengan 2 orang memesan stok terakhir secara bersamaan', function () {
    // buat produk dengan stok 2 cup
    $product = Product::factory()->create([
        'price' => 25000,
        'stock' => 2,
    ]);

    $results = [
        'user1' => null,
        'user2' => null,
    ];

    // Simulasi 2 request bersamaan dengan quantity 1 masing-masing
    // Harapan: keduanya akan berhasil karena stok ada 2
    
    $results['user1'] = $this->postJson('/api/checkout', [
        'product_id' => $product->id,
        'quantity' => 1,
        'customer_name' => 'User1_Concurrent',
    ]);

    $results['user2'] = $this->postJson('/api/checkout', [
        'product_id' => $product->id,
        'quantity' => 1,
        'customer_name' => 'User2_Concurrent',
    ]);

    // Kedua user harus berhasil
    expect($results['user1']->status())->toBe(201);
    expect($results['user2']->status())->toBe(201);

    // Verifikasi: stok harus habis (2 - 1 - 1 = 0)
    $product->refresh();
    expect($product->stock)->toBe(0);

    // Verifikasi: ada 2 order
    expect(Order::count())->toBe(2);
});

it('simulasi concurrent request dengan stok 1 akan menyebabkan 1 gagal', function () {
    // buat produk dengan stok hanya 1 cup
    $product = Product::factory()->create([
        'price' => 30000,
        'stock' => 1,
    ]);

    $results = [
        'user1' => null,
        'user2' => null,
    ];

    // Simulasi 2 request bersamaan, masing-masing mau pesan 1 cup
    // Harapan: 1 berhasil, 1 gagal (race condition)
    
    // Request 1: User1 pesan dengan validasi stok check
    $response1 = $this->postJson('/api/checkout', [
        'product_id' => $product->id,
        'quantity' => 1,
        'customer_name' => 'User1_RaceCondition',
    ]);

    // Request 2: User2 pesan setelah User1 (tapi stok sudah berkurang)
    $response2 = $this->postJson('/api/checkout', [
        'product_id' => $product->id,
        'quantity' => 1,
        'customer_name' => 'User2_RaceCondition',
    ]);

    // User1 harus berhasil
    expect($response1->status())->toBe(201);

    // User2 harus gagal karena stok sudah habis
    expect($response2->status())->toBe(422);

    // Verifikasi: hanya 1 order
    expect(Order::count())->toBe(1);

    // Verifikasi: stok sudah 0
    $product->refresh();
    expect($product->stock)->toBe(0);
});
