<?php
require_once 'config.php';
$page_title = 'Form Pemesanan Online - Pandu Jati Furniture';

$success_order = null;
$error_msg = null;

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name   = trim($_POST['customer_name'] ?? '');
    $customer_phone  = trim($_POST['customer_phone'] ?? '');
    $customer_email  = trim($_POST['customer_email'] ?? '');
    $address         = trim($_POST['address'] ?? '');
    $city            = trim($_POST['city'] ?? '');
    $province        = trim($_POST['province'] ?? '');
    $postal_code     = trim($_POST['postal_code'] ?? '');
    $product_id      = (int)($_POST['product_id'] ?? 0);
    $quantity        = max(1, (int)($_POST['quantity'] ?? 1));
    $finishing       = trim($_POST['finishing'] ?? 'Natural Teak Doff');
    $shipping_idx    = (int)($_POST['shipping_option'] ?? 0);
    $payment_term    = trim($_POST['payment_term'] ?? 'DP 50%');
    $customer_notes  = trim($_POST['customer_notes'] ?? '');

    if (empty($customer_name) || empty($customer_phone) || empty($address) || empty($city)) {
        $error_msg = "Harap lengkapi semua data nama, nomor telepon/WhatsApp, dan alamat pengiriman.";
    } else {
        // Ambil Data Produk
        $p_stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $p_stmt->execute([$product_id]);
        $prod = $p_stmt->fetch();

        if (!$prod) {
            $error_msg = "Produk tidak valid atau sudah tidak tersedia.";
        } else {
            // Kalkulasi Ongkir
            $shipping_item = $business['shipping_options'][$shipping_idx] ?? $business['shipping_options'][0];
            $shipping_cost = (float)$shipping_item['cost'];
            $shipping_name = $shipping_item['name'];

            $subtotal = $prod['price'] * $quantity;
            $total_amount = $subtotal + $shipping_cost;

            // Generate Unique Order Number
            $order_number = generateOrderNumber();

            // Insert Database Transaction
            try {
                $pdo->beginTransaction();

                $ins_stmt = $pdo->prepare("INSERT INTO orders 
                    (order_number, customer_name, customer_phone, customer_email, address, city, province, postal_code, shipping_method, shipping_cost, payment_method, subtotal, total_amount, order_status, payment_status, customer_notes) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'PENDING', 'UNPAID', ?)");
                
                $ins_stmt->execute([
                    $order_number,
                    $customer_name,
                    $customer_phone,
                    $customer_email,
                    $address,
                    $city,
                    $province,
                    $postal_code,
                    $shipping_name,
                    $shipping_cost,
                    $payment_term,
                    $subtotal,
                    $total_amount,
                    $customer_notes
                ]);

                $order_id = $pdo->lastInsertId();

                // Insert Order Item
                $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, finishing_choice, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $item_stmt->execute([
                    $order_id,
                    $prod['id'],
                    $prod['name'],
                    $prod['price'],
                    $quantity,
                    $finishing,
                    $subtotal
                ]);

                // Insert Status History
                $hist_stmt = $pdo->prepare("INSERT INTO order_status_history (order_id, status, note) VALUES (?, 'PENDING', 'Pesanan online berhasil dibuat oleh pelanggan.')");
                $hist_stmt->execute([$order_id]);

                $pdo->commit();

                // Simpan ke variable success
                $success_order = [
                    'order_number' => $order_number,
                    'customer_name' => $customer_name,
                    'customer_phone' => $customer_phone,
                    'product_name' => $prod['name'],
                    'quantity' => $quantity,
                    'total_amount' => $total_amount,
                    'payment_term' => $payment_term,
                    'address' => "$address, $city, $province"
                ];

            } catch (Exception $e) {
                $pdo->rollBack();
                $error_msg = "Gagal memproses pesanan: " . $e->getMessage();
            }
        }
    }
}

// Prefill Product jika ada parameter produk_id
$prefill_id = (int)($_GET['produk_id'] ?? 0);
$all_prods = $pdo->query("SELECT id, name, price, stock, image_url FROM products WHERE is_active = 1 ORDER BY name ASC")->fetchAll();

include 'includes/header.php';
?>

<div class="bg-teak-950 text-white py-12 wood-texture">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-2">
        <span class="text-xs font-bold text-amberwood-400 uppercase tracking-widest">Sistem Transaksi Resmi</span>
        <h1 class="font-serif text-3xl sm:text-4xl font-bold">Form Pemesanan Online</h1>
        <p class="text-xs sm:text-sm text-teak-300 max-w-xl mx-auto font-light">
            Setiap pesanan mendapatkan Nomor Order ID unik resmi yang dapat Anda lacak perkembangannya secara live.
        </p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <?php if ($success_order): ?>
    <!-- Halaman Sukses Order -->
    <div class="bg-white rounded-3xl border border-teak-200/80 p-8 sm:p-12 shadow-xl space-y-8 text-center">
        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto shadow-inner">
            <i data-lucide="check-circle" class="w-10 h-10"></i>
        </div>

        <div class="space-y-2">
            <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Pesanan Berhasil Dibuat</span>
            <h2 class="font-serif text-2xl sm:text-3xl font-bold text-teak-950">Terima Kasih, <?= htmlspecialchars($success_order['customer_name']) ?>!</h2>
            <p class="text-xs sm:text-sm text-teak-600 max-w-md mx-auto">
                Pesanan Anda telah tercatat di sistem workshop kami. Silakan simpan nomor pesanan berikut untuk melacak status pengerjaan kayu dan pengiriman:
            </p>
        </div>

        <!-- Box Nomor Pesanan -->
        <div class="bg-teak-50 border-2 border-dashed border-amberwood-400 p-6 rounded-2xl max-w-md mx-auto space-y-2">
            <span class="text-[10px] text-teak-500 uppercase tracking-widest block font-bold">Nomor Pesanan Unik Anda</span>
            <span class="font-mono text-2xl sm:text-3xl font-bold text-teak-950 tracking-wider block selection:bg-amberwood-200">
                <?= $success_order['order_number'] ?>
            </span>
            <span class="text-[11px] text-teak-500 block">Gunakan kode ini di halaman "Cek Pesanan"</span>
        </div>

        <!-- Ringkasan Singkat -->
        <div class="bg-teak-50/50 rounded-2xl p-6 text-left max-w-lg mx-auto text-xs space-y-2 border border-teak-200">
            <div class="flex justify-between border-b border-teak-100 pb-2">
                <span class="text-teak-500">Produk</span>
                <span class="font-semibold text-teak-900"><?= htmlspecialchars($success_order['product_name']) ?> (<?= $success_order['quantity'] ?>x)</span>
            </div>
            <div class="flex justify-between border-b border-teak-100 pb-2">
                <span class="text-teak-500">Total Pembayaran</span>
                <span class="font-bold text-base text-amberwood-700 font-serif"><?= formatRupiah($success_order['total_amount']) ?></span>
            </div>
            <div class="flex justify-between border-b border-teak-100 pb-2">
                <span class="text-teak-500">Ketentuan Bayar</span>
                <span class="font-semibold text-teak-900"><?= htmlspecialchars($success_order['payment_term']) ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-teak-500">Tujuan Kirim</span>
                <span class="font-semibold text-teak-900 text-right"><?= htmlspecialchars($success_order['address']) ?></span>
            </div>
        </div>

        <!-- Rekening Pembayaran -->
        <div class="max-w-lg mx-auto p-4 bg-amber-50 border border-amber-200 rounded-2xl text-left text-xs space-y-3">
            <h4 class="font-bold text-amber-900 flex items-center gap-1.5">
                <i data-lucide="credit-card" class="w-4 h-4 text-amber-700"></i> Rekening Transfer Resmi:
            </h4>
            <div class="space-y-1.5">
                <?php foreach ($business['bank_accounts'] as $b): ?>
                <div class="flex justify-between items-center bg-white p-2.5 rounded-xl border border-amber-200">
                    <span class="font-bold text-teak-900">Bank <?= $b['bank'] ?></span>
                    <span class="font-mono font-bold text-amber-900"><?= $b['number'] ?></span>
                    <span class="text-[10px] text-teak-500">(a/n <?= $b['holder'] ?>)</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Tombol Aksi WhatsApp & Tracking -->
        <?php
        $wa_msg = "Halo Pandu Jati Furniture,\nSaya ingin melakukan pemesanan.\n\n"
                . "Nomor Pesanan: " . $success_order['order_number'] . "\n"
                . "Nama: " . $success_order['customer_name'] . "\n"
                . "Produk: " . $success_order['product_name'] . " (" . $success_order['quantity'] . "x)\n"
                . "Total: " . formatRupiah($success_order['total_amount']) . "\n"
                . "Alamat: " . $success_order['address'] . "\n\n"
                . "Mohon dikonfirmasi. Terima kasih.";
        $wa_url = "https://wa.me/" . $business['whatsapp'] . "?text=" . urlencode($wa_msg);
        ?>
        <div class="pt-4 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="<?= $wa_url ?>" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 px-8 rounded-full text-xs sm:text-sm flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/20 transition">
                <i data-lucide="message-circle" class="w-5 h-5"></i> Kirim Detail Pesanan ke WhatsApp
            </a>
            <a href="cek-pesanan.php?id=<?= $success_order['order_number'] ?>" class="bg-teak-900 hover:bg-teak-800 text-white font-bold py-3.5 px-8 rounded-full text-xs sm:text-sm flex items-center justify-center gap-2 shadow transition">
                <i data-lucide="search" class="w-4 h-4 text-amberwood-400"></i> Cek Status Pesanan
            </a>
        </div>
    </div>

    <?php else: ?>

    <?php if ($error_msg): ?>
    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm flex items-center gap-2">
        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 flex-shrink-0"></i>
        <span><?= htmlspecialchars($error_msg) ?></span>
    </div>
    <?php endif; ?>

    <!-- Form Checkout -->
    <form method="POST" action="pesanan.php" class="bg-white rounded-3xl border border-teak-200/80 p-6 sm:p-10 shadow-sm space-y-8">
        <!-- Section 1: Produk yang Dipesan -->
        <div>
            <h3 class="font-serif font-bold text-lg text-teak-950 border-b border-teak-100 pb-3 mb-4 flex items-center gap-2">
                <i data-lucide="package" class="w-5 h-5 text-amberwood-600"></i>
                1. Pilih Produk Furniture
            </h3>
            
            <div class="space-y-4 text-xs">
                <div>
                    <label class="block font-semibold text-teak-800 mb-1.5">Pilih Produk *</label>
                    <select name="product_id" id="product_select" required class="w-full px-4 py-3 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500 bg-white">
                        <option value="">-- Pilih Produk dari Katalog --</option>
                        <?php foreach ($all_prods as $pr): ?>
                        <option value="<?= $pr['id'] ?>" data-price="<?= $pr['price'] ?>" <?= ($prefill_id === $pr['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($pr['name']) ?> - <?= formatRupiah($pr['price']) ?> (Stok: <?= $pr['stock'] ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-teak-800 mb-1.5">Jumlah Unit *</label>
                        <input type="number" name="quantity" id="quantity_input" min="1" max="50" value="1" required class="w-full px-4 py-3 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-teak-800 mb-1.5">Pilihan Finishing Warna Kayu</label>
                        <select name="finishing" class="w-full px-4 py-3 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500 bg-white">
                            <option value="Natural Teak Melamine Doff">Natural Teak Melamine Doff (Rekomendasi)</option>
                            <option value="Walnut Brown Matte">Walnut Brown Matte</option>
                            <option value="Salak Brown Gloss">Salak Brown Gloss</option>
                            <option value="Bleaching Modern Teak">Bleaching Modern Teak (Serat Muda)</option>
                            <option value="Duco Putih Kombinasi Natural">Duco Putih Kombinasi Natural</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Data Pelanggan -->
        <div>
            <h3 class="font-serif font-bold text-lg text-teak-950 border-b border-teak-100 pb-3 mb-4 flex items-center gap-2">
                <i data-lucide="user" class="w-5 h-5 text-amberwood-600"></i>
                2. Data Pemesan &amp; Kontak WhatsApp
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-teak-800 mb-1.5">Nama Lengkap *</label>
                    <input type="text" name="customer_name" required placeholder="Contoh: Bpk. Bambang Sudirman" class="w-full px-4 py-3 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500">
                </div>
                <div>
                    <label class="block font-semibold text-teak-800 mb-1.5">Nomor WhatsApp Aktif *</label>
                    <input type="tel" name="customer_phone" required placeholder="Contoh: 081234567890" class="w-full px-4 py-3 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block font-semibold text-teak-800 mb-1.5">Alamat Email (Opsional)</label>
                    <input type="email" name="customer_email" placeholder="bambang@gmail.com" class="w-full px-4 py-3 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500">
                </div>
            </div>
        </div>

        <!-- Section 3: Alamat Pengiriman -->
        <div>
            <h3 class="font-serif font-bold text-lg text-teak-950 border-b border-teak-100 pb-3 mb-4 flex items-center gap-2">
                <i data-lucide="map-pin" class="w-5 h-5 text-amberwood-600"></i>
                3. Alamat Tujuan Pengiriman
            </h3>

            <div class="space-y-4 text-xs">
                <div>
                    <label class="block font-semibold text-teak-800 mb-1.5">Alamat Lengkap (Nama Jalan, No. Rumah, RT/RW, Kelurahan, Kecamatan) *</label>
                    <textarea name="address" required rows="2" placeholder="Jl. Kemang Raya No. 42, RT 02/05..." class="w-full px-4 py-3 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500"></textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-semibold text-teak-800 mb-1.5">Kota / Kabupaten *</label>
                        <input type="text" name="city" required placeholder="Jakarta Selatan" class="w-full px-4 py-3 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-teak-800 mb-1.5">Provinsi *</label>
                        <input type="text" name="province" required placeholder="DKI Jakarta" class="w-full px-4 py-3 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-teak-800 mb-1.5">Kode Pos</label>
                        <input type="text" name="postal_code" placeholder="12730" class="w-full px-4 py-3 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Pengiriman & Pembayaran -->
        <div>
            <h3 class="font-serif font-bold text-lg text-teak-950 border-b border-teak-100 pb-3 mb-4 flex items-center gap-2">
                <i data-lucide="truck" class="w-5 h-5 text-amberwood-600"></i>
                4. Ekspedisi Kargo &amp; Ketentuan Pembayaran
            </h3>

            <div class="space-y-4 text-xs">
                <div>
                    <label class="block font-semibold text-teak-800 mb-1.5">Pilihan Pengiriman Kargo Jepara</label>
                    <div class="space-y-2">
                        <?php foreach ($business['shipping_options'] as $idx => $ship): ?>
                        <label class="flex items-center justify-between p-3.5 rounded-2xl border border-teak-200 hover:border-amberwood-500 cursor-pointer transition">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="shipping_option" value="<?= $idx ?>" <?= $idx === 0 ? 'checked' : '' ?> class="text-amberwood-600 focus:ring-amberwood-500">
                                <div>
                                    <span class="font-semibold text-teak-900 block"><?= htmlspecialchars($ship['name']) ?></span>
                                    <span class="text-teak-500 text-[11px]"><?= htmlspecialchars($ship['time']) ?></span>
                                </div>
                            </div>
                            <span class="font-bold text-teak-950"><?= formatRupiah($ship['cost']) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block font-semibold text-teak-800 mb-1.5">Metode / Termin Pembayaran</label>
                        <select name="payment_term" class="w-full px-4 py-3 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500 bg-white">
                            <option value="Transfer Bank BCA (DP 50%)">Transfer Bank BCA (DP 50% Produksi Mulai)</option>
                            <option value="Transfer Bank Mandiri (DP 50%)">Transfer Bank Mandiri (DP 50%)</option>
                            <option value="Transfer Bank BRI (DP 50%)">Transfer Bank BRI (DP 50%)</option>
                            <option value="Transfer Lunas 100%">Pembayaran Lunas 100% di Awal</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-teak-800 mb-1.5">Catatan Khusus Pesanan (Opsional)</label>
                        <input type="text" name="customer_notes" placeholder="Contoh: Mohon dipilihkan serat kayu yang rapat..." class="w-full px-4 py-3 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-4 border-t border-teak-100">
            <button type="submit" class="w-full bg-teak-900 hover:bg-amberwood-600 text-white font-bold py-4 rounded-2xl text-center transition flex items-center justify-center gap-2 shadow-lg text-sm sm:text-base">
                <i data-lucide="check" class="w-5 h-5 text-amberwood-400"></i> Buat Pesanan Sekarang &amp; Dapatkan Nomor Pesanan Unik
            </button>
            <p class="text-[11px] text-teak-500 text-center mt-3">
                Dengan menekan tombol di atas, pesanan Anda akan dicatat dan Anda akan mendapatkan nomor ID pesanan unik untuk pengecekan status pengerjaan mebel.
            </p>
        </div>
    </form>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
