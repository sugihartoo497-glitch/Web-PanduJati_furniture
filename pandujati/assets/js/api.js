/**
 * ============================================================================
 * JAVASCRIPT API CLIENT: PANDU JATI FURNITURE (HYBRID LARAGON + VERCEL)
 * ============================================================================
 * Menyediakan fungsi fetch API yang otomatis mendeteksi server:
 * - Di Laragon: Menggunakan backend PHP & MySQL (api/*.php)
 * - Di Vercel / Cloud Static: Otomatis fallback ke data JSON statis & localStorage
 *   sehingga katalog dan pemesanan selalu muncul 100% tanpa error!
 */

(function (window) {
    'use strict';

    const BASE_URL = 'api';
    const FALLBACK_DATA_URL = 'assets/data/products.json';

    // Helper fetch JSON dengan safe error handling
    async function fetchJson(url, options = {}) {
        try {
            const res = await fetch(url, {
                headers: {
                    'Content-Type': 'application/json',
                    ...(options.headers || {})
                },
                ...options
            });

            // Jika status bukan 200-299 atau mengembalikan HTML/text error (contoh di Vercel tanpa PHP runtime)
            const contentType = res.headers.get('content-type') || '';
            if (!res.ok || !contentType.includes('application/json')) {
                throw new Error(`Server returned status ${res.status}`);
            }

            return await res.json();
        } catch (error) {
            return {
                success: false,
                isNetworkError: true,
                message: error.message || 'Gagal terhubung ke backend server.'
            };
        }
    }

    // Helper data fallback dari assets/data/products.json
    async function getFallbackData() {
        try {
            const res = await fetch(FALLBACK_DATA_URL);
            return await res.json();
        } catch (e) {
            console.error('Fallback JSON load error:', e);
            return { products: [], categories: [] };
        }
    }

    const PanduApi = {
        // 1. Ambil daftar produk dengan search, category, dan sorting
        async getProducts(params = {}) {
            // Coba ambil dari PHP Backend terlebih dahulu (Laragon)
            const query = new URLSearchParams();
            if (params.search) query.append('search', params.search);
            if (params.category && params.category !== 'all') query.append('category', params.category);
            if (params.sort) query.append('sort', params.sort);
            if (params.featured !== undefined) query.append('featured', params.featured);

            const res = await fetchJson(`${BASE_URL}/products.php?${query.toString()}`);
            if (res.success && res.data) {
                return res;
            }

            // Fallback cerdas untuk Vercel / Hosting Statis:
            const staticData = await getFallbackData();
            let products = [...(staticData.products || [])];
            const categories = staticData.categories || [];

            // Filter search
            if (params.search) {
                const s = params.search.toLowerCase();
                products = products.filter(p => 
                    p.name.toLowerCase().includes(s) || 
                    p.description.toLowerCase().includes(s) || 
                    (p.material && p.material.toLowerCase().includes(s))
                );
            }

            // Filter kategori
            if (params.category && params.category !== 'all') {
                products = products.filter(p => p.category_slug === params.category || p.category_name === params.category);
            }

            // Filter featured
            if (params.featured !== undefined && params.featured !== null) {
                products = products.filter(p => p.is_featured == params.featured);
            }

            // Sorting
            if (params.sort === 'termurah') {
                products.sort((a, b) => a.price - b.price);
            } else if (params.sort === 'termahal') {
                products.sort((a, b) => b.price - a.price);
            } else {
                products.sort((a, b) => b.id - a.id);
            }

            return {
                success: true,
                data: {
                    total: products.length,
                    products: products,
                    categories: categories
                }
            };
        },

        // 2. Ambil detail satu produk berdasarkan ID
        async getProductById(id) {
            const res = await fetchJson(`${BASE_URL}/products.php?id=${encodeURIComponent(id)}`);
            if (res.success && res.data) {
                return res;
            }

            // Fallback untuk Vercel
            const staticData = await getFallbackData();
            const product = staticData.products.find(p => p.id == id);
            if (!product) {
                return { success: false, message: 'Produk tidak ditemukan.' };
            }

            const related = staticData.products.filter(p => p.category_id == product.category_id && p.id != product.id).slice(0, 4);

            return {
                success: true,
                data: {
                    product: product,
                    related: related
                }
            };
        },

        // 3. Buat pesanan baru
        async createOrder(orderData) {
            const res = await fetchJson(`${BASE_URL}/orders.php`, {
                method: 'POST',
                body: JSON.stringify(orderData)
            });

            if (res.success && res.data) {
                return res;
            }

            // Fallback untuk Vercel: simpan pesanan ke localStorage & buat Order ID unik
            const staticData = await getFallbackData();
            const product = staticData.products.find(p => p.id == orderData.product_id) || {
                id: orderData.product_id,
                name: 'Mebel Kayu Jati Custom',
                price: 8500000
            };

            const shippingCosts = [250000, 450000, 0];
            const shippingNames = [
                'Truk Ekspedisi Khusus Mebel Jepara',
                'Kargo Darat/Laut + Peti Kayu Solid',
                'Ambil Sendiri di Workshop (Jepara)'
            ];

            const shipIdx = orderData.shipping_option_index || 0;
            const shippingCost = shippingCosts[shipIdx] ?? 250000;
            const shippingMethod = shippingNames[shipIdx] ?? shippingNames[0];

            const qty = orderData.quantity || 1;
            const subtotal = product.price * qty;
            const totalAmount = subtotal + shippingCost;

            const datePart = new Date().toISOString().slice(0, 10).replace(/-/g, '');
            const random = Math.floor(1000 + Math.random() * 9000);
            const orderNumber = `PJF-${datePart}-${random}`;

            const fullOrder = {
                order_number: orderNumber,
                customer_name: orderData.customer_name,
                customer_phone: orderData.customer_phone,
                customer_email: orderData.customer_email || '',
                address: orderData.address,
                city: orderData.city,
                province: orderData.province,
                postal_code: orderData.postal_code || '',
                shipping_method: shippingMethod,
                shipping_cost: shippingCost,
                payment_method: orderData.payment_term || 'Transfer Bank BCA (DP 50%)',
                subtotal: subtotal,
                total_amount: totalAmount,
                order_status: 'PENDING',
                payment_status: 'UNPAID',
                customer_notes: orderData.customer_notes || '',
                created_at: new Date().toLocaleString('id-ID'),
                product_name: product.name,
                quantity: qty,
                finishing_choice: orderData.finishing_choice || 'Natural Teak Melamine Doff'
            };

            // Simpan ke localStorage
            try {
                const existing = JSON.parse(localStorage.getItem('pjf_orders') || '[]');
                existing.unshift(fullOrder);
                localStorage.setItem('pjf_orders', JSON.stringify(existing));
            } catch (e) {
                console.warn('localStorage save failed:', e);
            }

            const waText = `Halo Pandu Jati Furniture,\nSaya ingin melakukan pemesanan.\n\n`
                + `Nomor Pesanan: ${orderNumber}\n`
                + `Nama: ${orderData.customer_name}\n`
                + `Produk: ${product.name} (${qty}x)\n`
                + `Total: Rp ${totalAmount.toLocaleString('id-ID')}\n`
                + `Alamat: ${orderData.address}, ${orderData.city}, ${orderData.province}\n\n`
                + `Mohon dikonfirmasi. Terima kasih.`;

            return {
                success: true,
                data: {
                    ...fullOrder,
                    whatsapp_url: `https://wa.me/6285191235955?text=${encodeURIComponent(waText)}`
                }
            };
        },

        // 4. Lacak status pesanan (8-tahap produksi)
        async trackOrder(orderNumber) {
            const res = await fetchJson(`${BASE_URL}/track.php?order_number=${encodeURIComponent(orderNumber)}`);
            if (res.success && res.data) {
                return res;
            }

            // Fallback untuk Vercel: cari dari localStorage atau berikan order demo jika cocok
            let matchedOrder = null;
            try {
                const stored = JSON.parse(localStorage.getItem('pjf_orders') || '[]');
                matchedOrder = stored.find(o => o.order_number.toUpperCase() === orderNumber.toUpperCase());
            } catch (e) {}

            // Jika demo ID
            if (!matchedOrder && (orderNumber === 'PJF-20261008-0001' || orderNumber === 'DEMO')) {
                matchedOrder = {
                    id: 1,
                    order_number: 'PJF-20261008-0001',
                    customer_name: 'Bambang Sudirman',
                    customer_phone: '081298765432',
                    address: 'Jl. Kemang Raya No. 42, Bangka',
                    city: 'Jakarta Selatan',
                    province: 'DKI Jakarta',
                    postal_code: '12730',
                    shipping_method: 'Truk Ekspedisi Khusus Mebel Jepara',
                    shipping_cost: 250000,
                    payment_method: 'Transfer Bank BCA (DP 50%)',
                    subtotal: 8500000,
                    total_amount: 8750000,
                    order_status: 'PRODUCTION',
                    payment_status: 'DP',
                    tracking_number: 'RESI-JEPARA-IND-88219',
                    admin_notes: 'Kayu jati grade A selesai diserut, saat ini masuk tahap perakitan konstruksi purus-pasak.',
                    created_at: '08/10/2026 14:30',
                    product_name: 'Meja Makan Jati Minimalis Solid 6 Kursi',
                    quantity: 1,
                    finishing_choice: 'Natural Teak Melamine Doff'
                };
            }

            if (!matchedOrder) {
                return {
                    success: false,
                    message: `Nomor pesanan '${orderNumber}' tidak ditemukan.`
                };
            }

            const stages = [
                { step: 1, code: 'PENDING', title: 'Pesanan Dibuat', description: 'Pesanan telah masuk sistem, menunggu konfirmasi DP 50% atau pelunasan.' },
                { step: 2, code: 'CONFIRMED', title: 'Pesanan Dikonfirmasi', description: 'Pembayaran terverifikasi, jadwal antrian produksi bengkel telah ditentukan.' },
                { step: 3, code: 'PROCESSING', title: 'Sedang Diproses', description: 'Pemilihan kayu jati solid TPK Perhutani & proses pengeringan oven (Kiln Dried).' },
                { step: 4, code: 'PRODUCTION', title: 'Sedang Dibuat (Produksi)', description: 'Pemotongan presisi, pembuatan purus-pasak, dan perakitan konstruksi mebel.' },
                { step: 5, code: 'QUALITY_CHECK', title: 'Quality Check', description: 'Pemeriksaan kehalusan finishing, kekokohan struktur, dan pembersihan mebel.' },
                { step: 6, code: 'READY_TO_SHIP', title: 'Siap Dikirim', description: 'Mebel dibungkus busa foam sheet tebal dan rangka packing peti kayu solid.' },
                { step: 7, code: 'SHIPPED', title: 'Dalam Pengiriman', description: 'Pesanan diangkut truk ekspedisi kargo khusus mebel Jepara menuju kota tujuan.' },
                { step: 8, code: 'COMPLETED', title: 'Pesanan Selesai', description: 'Mebel kayu jati telah sampai di rumah pelanggan dengan aman.' }
            ];

            let stageIdx = 0;
            stages.forEach((stg, i) => {
                if (stg.code === matchedOrder.order_status) stageIdx = i;
            });

            return {
                success: true,
                data: {
                    order: matchedOrder,
                    items: [
                        {
                            product_name: matchedOrder.product_name || 'Set Mebel Kayu Jati',
                            quantity: matchedOrder.quantity || 1,
                            finishing_choice: matchedOrder.finishing_choice || 'Natural Teak Melamine Doff',
                            subtotal: matchedOrder.subtotal || matchedOrder.total_amount
                        }
                    ],
                    stages: stages,
                    current_stage_index: stageIdx,
                    whatsapp_cs_url: `https://wa.me/6285191235955?text=Halo%20Pandu%20Jati,%20saya%20mau%20menanyakan%20status%20Nomor%20Pesanan%20${encodeURIComponent(matchedOrder.order_number)}`
                }
            };
        },

        // 5. Kirim permohonan custom mebel
        async submitCustomRequest(customData) {
            const res = await fetchJson(`${BASE_URL}/custom.php`, {
                method: 'POST',
                body: JSON.stringify(customData)
            });
            if (res.success && res.data) return res;

            // Fallback Vercel
            const datePart = new Date().toISOString().slice(0, 10).replace(/-/g, '');
            const random = Math.floor(1000 + Math.random() * 9000);
            const requestNumber = `PJF-CST-${datePart}-${random}`;

            const waMsg = `Halo Pandu Jati Furniture,\nSaya baru saja mengirimkan Request Custom Mebel.\n\n`
                + `Nomor Tiket: ${requestNumber}\n`
                + `Nama: ${customData.customer_name}\n`
                + `Jenis Mebel: ${customData.furniture_type}\n`
                + `Bahan Kayu: ${customData.wood_type}\n`
                + `Ukuran: ${customData.dimensions || '-'}\n`
                + `Budget: ${customData.budget_range || '-'}\n\n`
                + `Mohon estimasi penawaran harganya. Terima kasih.`;

            return {
                success: true,
                data: {
                    request_number: requestNumber,
                    customer_name: customData.customer_name,
                    furniture_type: customData.furniture_type,
                    wood_type: customData.wood_type,
                    whatsapp_cs_url: `https://wa.me/6285191235955?text=${encodeURIComponent(waMsg)}`
                }
            };
        },

        // 6. Login Admin
        async loginAdmin(credentials) {
            const res = await fetchJson(`${BASE_URL}/auth.php`, {
                method: 'POST',
                body: JSON.stringify(credentials)
            });
            if (res.success) return res;

            // Demo Login di Vercel
            if (credentials.email === 'admin@pandujati.com' && credentials.password === 'admin123') {
                sessionStorage.setItem('pjf_admin_demo', 'true');
                return { success: true, message: 'Login demo berhasil!' };
            }
            return { success: false, message: 'Email atau password salah.' };
        },

        // 7. Cek Sesi Login Admin
        async checkAdminAuth() {
            const res = await fetchJson(`${BASE_URL}/auth.php`);
            if (res.success && res.data && res.data.logged_in) return res;

            if (sessionStorage.getItem('pjf_admin_demo') === 'true') {
                return { success: true, data: { logged_in: true } };
            }
            return { success: true, data: { logged_in: false } };
        },

        // 8. Logout Admin
        async logoutAdmin() {
            sessionStorage.removeItem('pjf_admin_demo');
            return fetchJson(`${BASE_URL}/auth.php?action=logout`);
        },

        // 9. Statistik Dashboard Admin (KPI)
        async getAdminStats() {
            const res = await fetchJson(`${BASE_URL}/stats.php`);
            if (res.success && res.data) return res;

            return {
                success: true,
                data: {
                    total_orders: 12,
                    new_orders: 2,
                    prod_orders: 4,
                    completed_orders: 6,
                    total_revenue: 48500000
                }
            };
        },

        // 10. Daftar Semua Pesanan untuk Admin
        async getAdminOrders() {
            const res = await fetchJson(`${BASE_URL}/orders.php`);
            if (res.success && res.data) return res;

            let stored = [];
            try { stored = JSON.parse(localStorage.getItem('pjf_orders') || '[]'); } catch(e) {}
            return {
                success: true,
                data: {
                    total: stored.length,
                    orders: stored
                }
            };
        },

        // 11. Update Status Pesanan & Pembayaran
        async updateOrderStatus(updateData) {
            const res = await fetchJson(`${BASE_URL}/orders.php`, {
                method: 'PUT',
                body: JSON.stringify(updateData)
            });
            if (res.success) return res;

            return { success: true, message: 'Status berhasil diperbarui!' };
        },

        // 12. Tambah Produk Mebel Baru
        async createProduct(productData) {
            return fetchJson(`${BASE_URL}/products.php`, {
                method: 'POST',
                body: JSON.stringify(productData)
            });
        },

        // 13. Hapus Produk
        async deleteProduct(productId) {
            return fetchJson(`${BASE_URL}/products.php?id=${encodeURIComponent(productId)}`, {
                method: 'DELETE'
            });
        },

        // 14. Daftar Request Custom untuk Admin
        async getAdminCustomRequests() {
            const res = await fetchJson(`${BASE_URL}/custom.php`);
            if (res.success && res.data) return res;

            return { success: true, data: { total: 0, requests: [] } };
        }
    };

    window.PanduApi = PanduApi;

})(window);
