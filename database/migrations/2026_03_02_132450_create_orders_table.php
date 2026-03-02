<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            // Menghubungkan pesanan ke produk (Foreign Key)
            // constrained('products') memastikan produknya benar-benar ada di tabel products
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            
            $table->integer('quantity');       // Jumlah cup/porsi yang dibeli
            $table->integer('total_price');    // Hasil kalkulasi (Price di DB * Quantity)
            $table->string('customer_name');   // Nama pembeli
            
            // Status pesanan untuk memantau siklus data
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};