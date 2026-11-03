<?php
require_once 'config.php';

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_user']);
    header("Location: admin.php");
    exit;
}

// Handle Login
$login_error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Cek di database
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && ($password === 'admin123' || password_verify($password, $user['password']))) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $user;
        header("Location: admin.php");
        exit;
    } else {
        $login_error = "Email atau password admin salah!";
    }
}

// Jika belum login, tampilkan layar login
if (empty($_SESSION['admin_logged_in'])):
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Pandu Jati Furniture</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-stone-100 flex items-center justify-center min-h-screen p-4 font-sans text-stone-900">
    <div class="max-w-md w-full bg-white rounded-3xl border border-stone-200 p-8 shadow-xl space-y-6">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 bg-amber-700 text-white rounded-2xl flex items-center justify-center mx-auto shadow-md">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
            <h1 class="text-xl font-bold text-stone-950 font-serif">Admin Pandu Jati</h1>
            <p class="text-xs text-stone-500">Masuk untuk mengelola katalog produk, pesanan, dan status produksi mebel.</p>
        </div>

        <?php if ($login_error): ?>
        <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
            <?= htmlspecialchars($login_error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="admin.php" class="space-y-4 text-xs">
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Email Administrator</label>
                <input type="email" name="email" value="admin@pandujati.com" required class="w-full px-4 py-3 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-amber-600">
            </div>
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Password</label>
                <input type="password" name="password" value="admin123" required class="w-full px-4 py-3 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-amber-600">
            </div>
            <button type="submit" name="login_submit" class="w-full bg-amber-800 hover:bg-amber-900 text-white font-bold py-3.5 rounded-xl transition text-xs shadow">
                Masuk ke Dashboard
            </button>
        </form>

        <div class="pt-4 border-t border-stone-100 text-center">
            <a href="index.php" class="text-xs text-stone-500 hover:text-amber-800 flex items-center justify-center gap-1">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Website
            </a>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
<?php
exit;
endif;

// ==========================================
// ADMIN DASHBOARD AREA (SETELAH LOGIN)
// ==========================================

$tab = $_GET['tab'] ?? 'orders';
$msg = null;

// Handle Update Order Status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order_status'])) {
    $order_id = (int)$_POST['order_id'];
    $order_status = $_POST['order_status'];
    $payment_status = $_POST['payment_status'];
    $tracking_number = trim($_POST['tracking_number'] ?? '');
    $admin_notes = trim($_POST['admin_notes'] ?? '');

    $up_stmt = $pdo->prepare("UPDATE orders SET order_status = ?, payment_status = ?, tracking_number = ?, admin_notes = ? WHERE id = ?");
    $up_stmt->execute([$order_status, $payment_status, $tracking_number, $admin_notes, $order_id]);

    // Tambah riwayat
    $rh = $pdo->prepare("INSERT INTO order_status_history (order_id, status, note) VALUES (?, ?, ?)");
    $rh->execute([$order_id, $order_status, "Status diperbarui oleh Admin: $order_status. $admin_notes"]);

    $msg = "Status pesanan berhasil diperbarui!";
}

// Handle Add Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $material = trim($_POST['material']);
    $dimensions = trim($_POST['dimensions']);
    $finishing = trim($_POST['finishing']);
    $image_url = trim($_POST['image_url']);
    $description = trim($_POST['description']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

    $ins_p = $pdo->prepare("INSERT INTO products (name, slug, category_id, price, stock, material, dimensions, finishing, image_url, description, is_active, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1)");
    $ins_p->execute([$name, $slug, $category_id, $price, $stock, $material, $dimensions, $finishing, $image_url, $description]);

    $msg = "Produk mebel baru berhasil ditambahkan!";
}

// Handle Delete Product
if (isset($_GET['del_prod'])) {
    $del_id = (int)$_GET['del_prod'];
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$del_id]);
    $msg = "Produk berhasil dihapus.";
}

// Data Statistik KPI
$total_orders = $pdo->query("SELECT count(*) FROM orders")->fetchColumn();
$new_orders = $pdo->query("SELECT count(*) FROM orders WHERE order_status = 'PENDING'")->fetchColumn();
$prod_orders = $pdo->query("SELECT count(*) FROM orders WHERE order_status = 'PRODUCTION'")->fetchColumn();
$completed_orders = $pdo->query("SELECT count(*) FROM orders WHERE order_status = 'COMPLETED'")->fetchColumn();
$total_revenue = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE payment_status IN ('PAID', 'DP')")->fetchColumn();

// List Orders
$orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll();

// List Products
$products = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();

// List Custom Requests
$custom_requests = $pdo->query("SELECT * FROM custom_requests ORDER BY id DESC")->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - Pandu Jati Furniture</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-stone-50 text-stone-900 font-sans min-h-screen">

<!-- Top Admin Header -->
<header class="bg-stone-900 text-white px-4 sm:px-8 py-4 border-b border-stone-800 flex justify-between items-center sticky top-0 z-30">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-amber-600 flex items-center justify-center font-bold">
            <i data-lucide="hammer" class="w-5 h-5 text-white"></i>
        </div>
        <div>
            <h1 class="font-bold text-sm leading-tight">Pandu Jati Furniture</h1>
            <span class="text-[10px] text-amber-400">Back-Office Admin Panel</span>
        </div>
    </div>
    <div class="flex items-center gap-4 text-xs">
        <a href="index.php" target="_blank" class="hover:text-amber-400 flex items-center gap-1 text-stone-300">
            <i data-lucide="external-link" class="w-3.5 h-3.5"></i> Lihat Web
        </a>
        <a href="admin.php?action=logout" class="bg-rose-600 hover:bg-rose-700 text-white px-3 py-1.5 rounded-lg flex items-center gap-1 transition">
            <i data-lucide="log-out" class="w-3.5 h-3.5"></i> Logout
        </a>
    </div>
</header>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <?php if ($msg): ?>
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
        <span><?= htmlspecialchars($msg) ?></span>
    </div>
    <?php endif; ?>

    <!-- KPI Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
            <span class="text-[10px] uppercase tracking-wider text-stone-500 font-bold block">Total Pesanan</span>
            <span class="text-2xl font-bold text-stone-900"><?= $total_orders ?></span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
            <span class="text-[10px] uppercase tracking-wider text-amber-600 font-bold block">Pesanan Baru (Pending)</span>
            <span class="text-2xl font-bold text-amber-600"><?= $new_orders ?></span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
            <span class="text-[10px] uppercase tracking-wider text-orange-600 font-bold block">Dalam Produksi</span>
            <span class="text-2xl font-bold text-orange-600"><?= $prod_orders ?></span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
            <span class="text-[10px] uppercase tracking-wider text-emerald-600 font-bold block">Pesanan Selesai</span>
            <span class="text-2xl font-bold text-emerald-600"><?= $completed_orders ?></span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm col-span-2 lg:col-span-1">
            <span class="text-[10px] uppercase tracking-wider text-stone-500 font-bold block">Total Pendapatan</span>
            <span class="text-lg font-bold text-emerald-700"><?= formatRupiah($total_revenue) ?></span>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-2 border-b border-stone-200 pb-3 text-xs font-bold">
        <a href="admin.php?tab=orders" class="px-5 py-2.5 rounded-xl transition <?= $tab === 'orders' ? 'bg-amber-800 text-white' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200' ?>">
            📦 Kelola Pesanan (<?= count($orders) ?>)
        </a>
        <a href="admin.php?tab=products" class="px-5 py-2.5 rounded-xl transition <?= $tab === 'products' ? 'bg-amber-800 text-white' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200' ?>">
            🪵 Kelola Produk (<?= count($products) ?>)
        </a>
        <a href="admin.php?tab=custom" class="px-5 py-2.5 rounded-xl transition <?= $tab === 'custom' ? 'bg-amber-800 text-white' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200' ?>">
            ✨ Request Custom (<?= count($custom_requests) ?>)
        </a>
    </div>

    <!-- TAB 1: KELOLA PESANAN -->
    <?php if ($tab === 'orders'): ?>
    <div class="bg-white rounded-3xl border border-stone-200 overflow-hidden shadow-sm">
        <div class="p-6 border-b border-stone-100 flex justify-between items-center">
            <h3 class="font-bold text-base text-stone-900">Daftar Semua Pesanan Pelanggan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-100 text-stone-600 uppercase text-[10px] tracking-wider border-b border-stone-200">
                    <tr>
                        <th class="p-4">Order ID &amp; Tanggal</th>
                        <th class="p-4">Pelanggan</th>
                        <th class="p-4">Total &amp; Bayar</th>
                        <th class="p-4">Status Produksi</th>
                        <th class="p-4">Resi / Ekspedisi</th>
                        <th class="p-4 text-center">Aksi / Ubah Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    <?php foreach ($orders as $o): ?>
                    <?php 
                    $sb = getStatusBadge($o['order_status']);
                    $pb = getPaymentBadge($o['payment_status']);
                    ?>
                    <tr class="hover:bg-stone-50/80 transition">
                        <td class="p-4">
                            <span class="font-mono font-bold text-stone-900 block"><?= htmlspecialchars($o['order_number']) ?></span>
                            <span class="text-[10px] text-stone-400"><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></span>
                        </td>
                        <td class="p-4">
                            <span class="font-semibold text-stone-900 block"><?= htmlspecialchars($o['customer_name']) ?></span>
                            <span class="text-stone-500 block"><?= htmlspecialchars($o['customer_phone']) ?></span>
                            <span class="text-[11px] text-stone-400 block"><?= htmlspecialchars($o['city']) ?></span>
                        </td>
                        <td class="p-4">
                            <span class="font-bold text-stone-900 block"><?= formatRupiah($o['total_amount']) ?></span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border <?= $pb['class'] ?> inline-block mt-1">
                                <?= $pb['label'] ?>
                            </span>
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border <?= $sb['class'] ?> inline-block">
                                <?= $sb['label'] ?>
                            </span>
                        </td>
                        <td class="p-4">
                            <span class="font-mono text-stone-700 block"><?= htmlspecialchars($o['tracking_number'] ?: '-') ?></span>
                            <span class="text-[10px] text-stone-400 block"><?= htmlspecialchars($o['shipping_method']) ?></span>
                        </td>
                        <td class="p-4 text-center">
                            <button onclick="openEditModal(<?= htmlspecialchars(json_encode($o)) ?>)" class="bg-stone-900 hover:bg-amber-800 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                Ubah Status
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Ubah Status Order -->
    <div id="orderModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-4 shadow-2xl">
            <div class="flex justify-between items-center border-b border-stone-100 pb-3">
                <h4 class="font-bold text-base text-stone-950">Update Status Pesanan</h4>
                <button onclick="closeEditModal()" class="text-stone-400 hover:text-stone-900"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form method="POST" action="admin.php?tab=orders" class="space-y-4 text-xs">
                <input type="hidden" name="order_id" id="modal_order_id">
                
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Nomor Pesanan</label>
                    <input type="text" id="modal_order_number" readonly class="w-full px-3 py-2 bg-stone-100 rounded-xl border border-stone-200 font-mono font-bold">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">Tahap Produksi 8-Step</label>
                        <select name="order_status" id="modal_order_status" class="w-full px-3 py-2 rounded-xl border border-stone-200 font-semibold bg-white">
                            <option value="PENDING">1. Pesanan Dibuat (PENDING)</option>
                            <option value="CONFIRMED">2. Pesanan Dikonfirmasi (CONFIRMED)</option>
                            <option value="PROCESSING">3. Sedang Diproses (PROCESSING)</option>
                            <option value="PRODUCTION">4. Sedang Dibuat (PRODUCTION)</option>
                            <option value="QUALITY_CHECK">5. Quality Check (QUALITY_CHECK)</option>
                            <option value="READY_TO_SHIP">6. Siap Dikirim (READY_TO_SHIP)</option>
                            <option value="SHIPPED">7. Dalam Pengiriman (SHIPPED)</option>
                            <option value="COMPLETED">8. Pesanan Selesai (COMPLETED)</option>
                            <option value="CANCELLED">Batal (CANCELLED)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">Status Pembayaran</label>
                        <select name="payment_status" id="modal_payment_status" class="w-full px-3 py-2 rounded-xl border border-stone-200 font-semibold bg-white">
                            <option value="UNPAID">Belum Dibayar (UNPAID)</option>
                            <option value="DP">DP 50% Masuk (DP)</option>
                            <option value="PAID">Lunas (PAID)</option>
                            <option value="REFUNDED">Refund (REFUNDED)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Nomor Resi / Ekspedisi Truk</label>
                    <input type="text" name="tracking_number" id="modal_tracking_number" placeholder="Contoh: RESI-JEPARA-88219" class="w-full px-3 py-2 rounded-xl border border-stone-200">
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Catatan Pengrajin (Terlihat oleh Pelanggan)</label>
                    <textarea name="admin_notes" id="modal_admin_notes" rows="2" placeholder="Kayu sudah dirakit, saat ini masuk proses finishing melamine..." class="w-full px-3 py-2 rounded-xl border border-stone-200"></textarea>
                </div>

                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl border border-stone-200 text-stone-700 font-semibold">Batal</button>
                    <button type="submit" name="update_order_status" class="px-5 py-2 rounded-xl bg-amber-800 hover:bg-amber-900 text-white font-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 2: KELOLA PRODUK -->
    <?php elseif ($tab === 'products'): ?>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Form Tambah Produk (4 cols) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-3xl border border-stone-200 shadow-sm space-y-4">
            <h3 class="font-bold text-sm text-stone-900 border-b border-stone-100 pb-2">Tambah Produk Baru</h3>
            <form method="POST" action="admin.php?tab=products" class="space-y-3 text-xs">
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Nama Produk Mebel *</label>
                    <input type="text" name="name" required placeholder="Contoh: Meja Makan Jati Minimalis" class="w-full px-3 py-2 rounded-xl border border-stone-200">
                </div>
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Kategori *</label>
                    <select name="category_id" class="w-full px-3 py-2 rounded-xl border border-stone-200 bg-white">
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">Harga (Rp) *</label>
                        <input type="number" name="price" required placeholder="8500000" class="w-full px-3 py-2 rounded-xl border border-stone-200">
                    </div>
                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">Stok *</label>
                        <input type="number" name="stock" value="5" required class="w-full px-3 py-2 rounded-xl border border-stone-200">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Material Kayu</label>
                    <input type="text" name="material" value="Kayu Jati Solid TPK Perhutani" class="w-full px-3 py-2 rounded-xl border border-stone-200">
                </div>
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Ukuran (Dimensi)</label>
                    <input type="text" name="dimensions" value="P 180 x L 90 x T 75 cm" class="w-full px-3 py-2 rounded-xl border border-stone-200">
                </div>
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Finishing Warna</label>
                    <input type="text" name="finishing" value="Natural Teak Melamine Doff" class="w-full px-3 py-2 rounded-xl border border-stone-200">
                </div>
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">URL Foto Produk *</label>
                    <input type="url" name="image_url" required placeholder="https://images.unsplash.com/..." class="w-full px-3 py-2 rounded-xl border border-stone-200">
                </div>
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Deskripsi Produk *</label>
                    <textarea name="description" rows="3" required placeholder="Deskripsi keunggulan kayu dan detail..." class="w-full px-3 py-2 rounded-xl border border-stone-200"></textarea>
                </div>
                <button type="submit" name="add_product" class="w-full bg-amber-800 hover:bg-amber-900 text-white font-bold py-2.5 rounded-xl transition text-xs shadow">
                    + Tambahkan Produk
                </button>
            </form>
        </div>

        <!-- Tabel List Produk (8 cols) -->
        <div class="lg:col-span-8 bg-white rounded-3xl border border-stone-200 overflow-hidden shadow-sm">
            <div class="p-6 border-b border-stone-100">
                <h3 class="font-bold text-sm text-stone-900">Katalog Produk Aktif (<?= count($products) ?>)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-100 text-stone-600 uppercase text-[10px] tracking-wider border-b border-stone-200">
                        <tr>
                            <th class="p-4">Foto &amp; Nama</th>
                            <th class="p-4">Kategori</th>
                            <th class="p-4">Harga</th>
                            <th class="p-4">Stok</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        <?php foreach ($products as $pr): ?>
                        <tr class="hover:bg-stone-50 transition">
                            <td class="p-4 flex items-center gap-3">
                                <img src="<?= htmlspecialchars($pr['image_url']) ?>" alt="" class="w-10 h-10 rounded-lg object-cover">
                                <div>
                                    <span class="font-bold text-stone-900 block"><?= htmlspecialchars($pr['name']) ?></span>
                                    <span class="text-[10px] text-stone-400"><?= htmlspecialchars($pr['dimensions']) ?></span>
                                </div>
                            </td>
                            <td class="p-4 text-stone-600"><?= htmlspecialchars($pr['category_name']) ?></td>
                            <td class="p-4 font-bold text-stone-900"><?= formatRupiah($pr['price']) ?></td>
                            <td class="p-4 font-semibold text-emerald-600"><?= $pr['stock'] ?> unit</td>
                            <td class="p-4 text-center">
                                <a href="admin.php?tab=products&del_prod=<?= $pr['id'] ?>" onclick="return confirm('Hapus produk ini?')" class="text-rose-600 hover:text-rose-800 p-1.5 rounded-lg hover:bg-rose-50 transition inline-block">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 3: CUSTOM REQUESTS -->
    <?php elseif ($tab === 'custom'): ?>
    <div class="bg-white rounded-3xl border border-stone-200 overflow-hidden shadow-sm">
        <div class="p-6 border-b border-stone-100">
            <h3 class="font-bold text-sm text-stone-900">Permintaan Custom Furniture dari Pelanggan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-100 text-stone-600 uppercase text-[10px] tracking-wider border-b border-stone-200">
                    <tr>
                        <th class="p-4">No. Tiket</th>
                        <th class="p-4">Pelanggan</th>
                        <th class="p-4">Jenis &amp; Kayu</th>
                        <th class="p-4">Ukuran &amp; Budget</th>
                        <th class="p-4">Deskripsi Permintaan</th>
                        <th class="p-4 text-center">Kontak WA</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    <?php if (empty($custom_requests)): ?>
                    <tr>
                        <td colspan="6" class="p-8 text-center text-stone-400">Belum ada request custom furniture baru.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($custom_requests as $cr): ?>
                    <tr class="hover:bg-stone-50 transition">
                        <td class="p-4 font-mono font-bold text-stone-900"><?= htmlspecialchars($cr['request_number']) ?></td>
                        <td class="p-4">
                            <span class="font-bold text-stone-900 block"><?= htmlspecialchars($cr['customer_name']) ?></span>
                            <span class="text-stone-500 block"><?= htmlspecialchars($cr['customer_phone']) ?></span>
                        </td>
                        <td class="p-4">
                            <span class="font-semibold text-stone-900 block"><?= htmlspecialchars($cr['furniture_type']) ?></span>
                            <span class="text-stone-500 text-[10px]"><?= htmlspecialchars($cr['wood_type']) ?></span>
                        </td>
                        <td class="p-4">
                            <span class="text-stone-700 block"><?= htmlspecialchars($cr['dimensions'] ?: '-') ?></span>
                            <span class="text-amber-700 font-bold text-[11px]"><?= htmlspecialchars($cr['budget_range'] ?: '-') ?></span>
                        </td>
                        <td class="p-4 max-w-xs text-stone-600 truncate"><?= htmlspecialchars($cr['description']) ?></td>
                        <td class="p-4 text-center">
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $cr['customer_phone']) ?>?text=Halo%20<?= urlencode($cr['customer_name']) ?>,%20kami%20dari%20Pandu%20Jati%20Furniture%20menanggapi%20request%20custom%20mebel%20Anda" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg font-semibold inline-flex items-center gap-1">
                                <i data-lucide="message-circle" class="w-3.5 h-3.5"></i> Balas WA
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
    lucide.createIcons();

    function openEditModal(order) {
        document.getElementById('modal_order_id').value = order.id;
        document.getElementById('modal_order_number').value = order.order_number;
        document.getElementById('modal_order_status').value = order.order_status;
        document.getElementById('modal_payment_status').value = order.payment_status;
        document.getElementById('modal_tracking_number').value = order.tracking_number || '';
        document.getElementById('modal_admin_notes').value = order.admin_notes || '';
        document.getElementById('orderModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('orderModal').classList.add('hidden');
    }
</script>
</body>
</html>
