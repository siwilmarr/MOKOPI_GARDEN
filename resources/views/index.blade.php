<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mokopi Garden - Coffee Shop</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Instrument Sans', sans-serif;
            background: linear-gradient(135deg, #1b1b18 0%, #2d2d28 100%);
            color: #1b1b18;
        }

        .navbar {
            background: #fff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .navbar h1 {
            font-size: 1.5rem;
            color: #6B4423;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .hero {
            text-align: center;
            color: white;
            padding: 3rem 0;
        }

        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .hero p {
            font-size: 1.1rem;
            color: #ddd;
        }

        .products-section {
            margin-top: 3rem;
        }

        .section-title {
            font-size: 2rem;
            color: white;
            margin-bottom: 2rem;
            text-align: center;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .product-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
        }

        .product-name {
            font-size: 1.3rem;
            font-weight: 600;
            color: #6B4423;
            margin-bottom: 0.5rem;
        }

        .product-desc {
            color: #706f6c;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            min-height: 40px;
        }

        .product-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1b1b18;
            margin-bottom: 1rem;
        }

        .stock-info {
            font-size: 0.85rem;
            color: #999;
            margin-bottom: 1rem;
        }

        .btn {
            padding: 0.7rem 1.5rem;
            border: none;
            border-radius: 4px;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }

        .btn-primary {
            background: #6B4423;
            color: white;
            width: 100%;
        }

        .btn-primary:hover {
            background: #5a3a1e;
        }

        .btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        /* Modal Checkout */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 1.5rem;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        .modal h2 {
            color: #6B4423;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #1b1b18;
        }

        .form-group input {
            width: 100%;
            padding: 0.7rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.95rem;
        }

        .form-group input:focus {
            outline: none;
            border-color: #6B4423;
            box-shadow: 0 0 4px rgba(107, 68, 35, 0.3);
        }

        .order-summary {
            background: #f5f5f5;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .summary-row.total {
            font-weight: bold;
            font-size: 1.1rem;
            color: #6B4423;
            border-top: 1px solid #ddd;
            padding-top: 0.5rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 1rem;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #6B4423;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>☕ Mokopi Garden</h1>
        <p style="color: #666;">Kopi Terbaik Untuk Anda</p>
    </div>

    <div class="container">
        <div class="hero">
            <h1>Selamat Datang di Mokopi Garden</h1>
            <p>Nikmati kopi berkualitas premium dengan cita rasa autentik</p>
        </div>

        <div class="products-section">
            <h2 class="section-title">Menu Kopi Kami</h2>
            <div class="products-grid" id="productsContainer">
                <!-- Product cards akan dimuat di sini via JavaScript -->
                <p style="color: white; text-align: center;">Memuat produk...</p>
            </div>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div id="checkoutModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Pesan Kopi</h2>

            <div id="alertContainer"></div>
            <div class="loading" id="loadingSpinner">
                <div class="spinner"></div>
                <p>Memproses pesanan...</p>
            </div>

            <form id="checkoutForm" style="display: none;">
                <div class="order-summary" id="orderSummary">
                    <!-- Summary akan diisi oleh JavaScript -->
                </div>

                <div class="form-group">
                    <label for="quantity">Jumlah (cup):</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" required>
                </div>

                <div class="form-group">
                    <label for="customerName">Nama Anda:</label>
                    <input type="text" id="customerName" name="customerName" placeholder="Masukkan nama Anda" required>
                </div>

                <button type="submit" class="btn btn-primary">Pesan Sekarang</button>
            </form>
        </div>
    </div>

    <script>
        const API_BASE = '/api';
        let selectedProduct = null;

        // Fetch dan tampilkan produk
        async function loadProducts() {
            try {
                const response = await fetch(`${API_BASE}/products`);
                
                if (!response.ok) {
                    // Jika endpoint tidak ada, gunakan data dummy
                    displayDummyProducts();
                    return;
                }

                const products = await response.json();
                displayProducts(products.data || products);
            } catch (error) {
                console.warn('Menggunakan data dummy produk:', error);
                displayDummyProducts();
            }
        }

        // Data dummy jika API belum siap
        function displayDummyProducts() {
            const dummyProducts = [
                {
                    id: 1,
                    name: 'Espresso',
                    description: 'Kopi espresso murni dengan cita rasa kuat dan pekat',
                    price: 15000,
                    stock: 50
                },
                {
                    id: 2,
                    name: 'Americano',
                    description: 'Espresso dicampur air panas untuk rasa yang lebih ringan',
                    price: 18000,
                    stock: 45
                },
                {
                    id: 3,
                    name: 'Cappuccino',
                    description: 'Espresso dengan susu dan busa yang creamy',
                    price: 25000,
                    stock: 40
                },
                {
                    id: 4,
                    name: 'Latte',
                    description: 'Espresso dengan susu banyak untuk rasa yang smooth',
                    price: 25000,
                    stock: 35
                },
                {
                    id: 5,
                    name: 'Macchiato',
                    description: 'Espresso ditandai dengan sedikit busa susu',
                    price: 20000,
                    stock: 30
                },
                {
                    id: 6,
                    name: 'Mocha',
                    description: 'Kombinasi espresso, susu, dan cokelat yang nikmat',
                    price: 28000,
                    stock: 25
                }
            ];
            displayProducts(dummyProducts);
        }

        function displayProducts(products) {
            const container = document.getElementById('productsContainer');
            container.innerHTML = '';

            products.forEach(product => {
                const card = document.createElement('div');
                card.className = 'product-card';
                card.innerHTML = `
                    <div class="product-name">${product.name}</div>
                    <div class="product-desc">${product.description}</div>
                    <div class="product-price">Rp ${product.price.toLocaleString('id-ID')}</div>
                    <div class="stock-info">Stok: ${product.stock} cup</div>
                    <button class="btn btn-primary" onclick="openCheckout(${product.id}, '${product.name}', ${product.price}, ${product.stock})">
                        Pesan Sekarang
                    </button>
                `;
                container.appendChild(card);
            });
        }

        function openCheckout(productId, productName, productPrice, stock) {
            selectedProduct = { id: productId, name: productName, price: productPrice, stock };
            
            // Reset form
            document.getElementById('checkoutForm').style.display = 'block';
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('alertContainer').innerHTML = '';
            document.getElementById('quantity').value = '1';
            document.getElementById('customerName').value = '';
            document.getElementById('quantity').max = stock;

            updateOrderSummary();
            document.getElementById('checkoutModal').style.display = 'block';
        }

        function updateOrderSummary() {
            const quantity = parseInt(document.getElementById('quantity').value) || 1;
            const totalPrice = selectedProduct.price * quantity;

            document.getElementById('orderSummary').innerHTML = `
                <div class="summary-row">
                    <span>${selectedProduct.name}</span>
                    <span>Rp ${selectedProduct.price.toLocaleString('id-ID')}</span>
                </div>
                <div class="summary-row">
                    <span>Jumlah: ${quantity}</span>
                    <span></span>
                </div>
                <div class="summary-row total">
                    <span>Total Harga:</span>
                    <span>Rp ${totalPrice.toLocaleString('id-ID')}</span>
                </div>
                <p style="font-size: 0.85rem; color: #666; margin-top: 0.5rem;">
                    * Harga dihitung ulang di server untuk keamanan
                </p>
            `;
        }

        // Update summary saat quantity berubah
        document.addEventListener('DOMContentLoaded', function() {
            const quantityInput = document.getElementById('quantity');
            quantityInput.addEventListener('change', updateOrderSummary);
            quantityInput.addEventListener('input', updateOrderSummary);
        });

        // Modal close handler
        const modal = document.getElementById('checkoutModal');
        const closeBtn = document.querySelector('.close');

        closeBtn.onclick = () => {
            modal.style.display = 'none';
        };

        window.onclick = (event) => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        };

        // Form submit handler
        document.getElementById('checkoutForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const quantity = parseInt(document.getElementById('quantity').value);
            const customerName = document.getElementById('customerName').value;

            // Validasi
            if (quantity < 1 || quantity > selectedProduct.stock) {
                showAlert('Jumlah tidak valid', 'error');
                return;
            }

            if (!customerName.trim()) {
                showAlert('Nama tidak boleh kosong', 'error');
                return;
            }

            // Tampilkan loading
            document.getElementById('checkoutForm').style.display = 'none';
            document.getElementById('loadingSpinner').style.display = 'block';

            try {
                const response = await fetch(`${API_BASE}/checkout`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        product_id: selectedProduct.id,
                        quantity: quantity,
                        customer_name: customerName
                        // Perhatian: jangan kirim 'price' atau 'total_price' - hanya reference & qty
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    showAlert(`Pesanan berhasil dibuat! Nomor pesanan: #${data.data.id || 'N/A'}`, 'success');
                    
                    // Close modal setelah 2 detik
                    setTimeout(() => {
                        modal.style.display = 'none';
                        loadProducts(); // Reload produk untuk update stok
                    }, 2000);
                } else {
                    console.error('Server response error:', data);
                    showAlert(data.message || 'Gagal membuat pesanan', 'error');
                    document.getElementById('checkoutForm').style.display = 'block';
                    document.getElementById('loadingSpinner').style.display = 'none';
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan. Silakan coba lagi.', 'error');
                document.getElementById('checkoutForm').style.display = 'block';
                document.getElementById('loadingSpinner').style.display = 'none';
            }
        });

        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
            alertContainer.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;
        }

        // Load produk saat halaman dibuka
        loadProducts();
    </script>
</body>
</html>
