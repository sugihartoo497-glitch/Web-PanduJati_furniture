<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ? AND p.is_active = 1");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: katalog.php");
    exit;
}

$page_title = $product['name'] . ' - Pandu Jati Furniture';

// Produk Terkait
$related_stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? AND is_active = 1 LIMIT 4");
$related_stmt->execute([$product['category_id'], $product['id']]);
$related_products = $related_stmt->fetchAll();

include 'includes/header.php';
?>

<!-- Breadcrumbs -->
<div class="bg-teak-100/60 py-3 border-b border-teak-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-xs text-teak-600 flex items-center gap-2">
        <a href="index.php" class="hover:text-amberwood-600">Beranda</a>
        <span>/</span>
        <a href="katalog.php" class="hover:text-amberwood-600">Katalog</a>
        <span>/</span>
        <span class="text-teak-900 font-semibold line-clamp-1"><?= htmlspecialchars($product['name']) ?></span>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Foto Produk (5 cols) -->
        <div class="lg:col-span-6 space-y-4">
            <div class="rounded-3xl overflow-hidden border border-teak-200 shadow-md bg-teak-100 h-96 sm:h-[460px]">
                <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover">
            </div>
            <div class="grid grid-cols-3 gap-3 text-center text-xs text-teak-600">
                <div class="bg-white p-3 rounded-2xl border border-teak-200">
                    <i data-lucide="shield-check" class="w-5 h-5 mx-auto text-emerald-600 mb-1"></i>
                    <span>Kayu Jati Legal</span>
                </div>
                <div class="bg-white p-3 rounded-2xl border border-teak-200">
                    <i data-lucide="package" class="w-5 h-5 mx-auto text-blue-600 mb-1"></i>
                    <span>Packing Peti Kayu</span>
                </div>
                <div class="bg-white p-3 rounded-2xl border border-teak-200">
                    <i data-lucide="truck" class="w-5 h-5 mx-auto text-amberwood-600 mb-1"></i>
                    <span>Ekspedisi Truk</span>
                </div>
            </div>
        </div>

        <!-- Detail Spesifikasi & Tombol Order (7 cols) -->
        <div class="lg:col-span-6 space-y-6">
            <div>
                <span class="bg-amberwood-100 text-amberwood-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                    <?= htmlspecialchars($product['category_name']) ?>
                </span>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-teak-950 mt-3">
                    <?= htmlspecialchars($product['name']) ?>
                </h1>
                
                <div class="flex items-center gap-3 mt-4">
                    <span class="text-2xl sm:text-3xl font-bold text-teak-950 font-serif">
                        <?= formatRupiah($product['price']) ?>
                    </span>
                    <?php if ($product['original_price'] > $product['price']): ?>
                    <span class="text-sm text-teak-400 line-through">
                        <?= formatRupiah($product['original_price']) ?>
                    </span>
                    <span class="bg-rose-100 text-rose-700 text-xs font-semibold px-2 py-0.5 rounded-md">
                        Hemat <?= formatRupiah($product['original_price'] - $product['price']) ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Ringkasan Deskripsi -->
            <div class="bg-white p-6 rounded-3xl border border-teak-200/80 shadow-sm space-y-3">
                <h3 class="font-serif font-bold text-sm text-teak-900 border-b border-teak-100 pb-2">Deskripsi Produk</h3>
                <p class="text-xs sm:text-sm text-teak-700 leading-relaxed">
                    <?= nl2br(htmlspecialchars($product['description'])) ?>
                </p>
            </div>

            <!-- Tabel Spesifikasi Teknis -->
            <div class="bg-white p-6 rounded-3xl border border-teak-200/80 shadow-sm space-y-3">
                <h3 class="font-serif font-bold text-sm text-teak-900 border-b border-teak-100 pb-2">Spesifikasi Mebel</h3>
                <div class="grid grid-cols-2 gap-y-3 text-xs">
                    <div>
                        <span class="text-teak-500 block">Material Utama</span>
                        <span class="font-semibold text-teak-900"><?= htmlspecialchars($product['material']) ?></span>
                    </div>
                    <div>
                        <span class="text-teak-500 block">Dimensi Ukuran</span>
                        <span class="font-semibold text-teak-900"><?= htmlspecialchars($product['dimensions']) ?></span>
                    </div>
                    <div>
                        <span class="text-teak-500 block">Finishing Warna</span>
                        <span class="font-semibold text-teak-900"><?= htmlspecialchars($product['finishing']) ?></span>
                    </div>
                    <div>
                        <span class="text-teak-500 block">Estimasi Pengerjaan</span>
                        <span class="font-semibold text-teak-900"><?= htmlspecialchars($product['lead_time']) ?></span>
                    </div>
                    <div>
                        <span class="text-teak-500 block">Estimasi Berat</span>
                        <span class="font-semibold text-teak-900"><?= htmlspecialchars($product['weight']) ?></span>
                    </div>
                    <div>
                        <span class="text-teak-500 block">Ketersediaan Stok</span>
                        <span class="font-semibold text-emerald-600 flex items-center gap-1">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Ready Stock (<?= $product['stock'] ?> unit)
                        </span>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi Pesan -->
            <div class="space-y-3 pt-2">
                <a href="pesanan.php?produk_id=<?= $product['id'] ?>" class="w-full bg-teak-900 hover:bg-amberwood-600 text-white font-bold py-4 rounded-2xl text-center transition flex items-center justify-center gap-2 shadow-lg shadow-teak-950/10 text-sm">
                    <i data-lucide="shopping-cart" class="w-5 h-5"></i> Pesan Produk Ini Secara Online
                </a>
                
                <div class="grid grid-cols-2 gap-3">
                    <a href="https://wa.me/<?= $business['whatsapp'] ?>?text=Halo%20Pandu%20Jati,%20saya%20tertarik%20dengan%20produk%20*<?= urlencode($product['name']) ?>*%20seharga%20<?= urlencode(formatRupiah($product['price'])) ?>" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-2xl text-center transition flex items-center justify-center gap-2 text-xs shadow">
                        <i data-lucide="message-circle" class="w-4 h-4"></i> Chat via WhatsApp
                    </a>
                    <a href="custom.php" class="bg-teak-100 hover:bg-teak-200 text-teak-900 font-semibold py-3 rounded-2xl text-center transition flex items-center justify-center gap-2 text-xs border border-teak-300">
                        <i data-lucide="compass" class="w-4 h-4"></i> Request Custom Ukuran
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Produk Terkait -->
    <?php if (!empty($related_products)): ?>
    <div class="mt-16 pt-10 border-t border-teak-200">
        <h3 class="font-serif font-bold text-xl text-teak-950 mb-6">Produk Terkait Lainnya</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($related_products as $rp): ?>
            <div class="bg-white rounded-2xl border border-teak-200/80 overflow-hidden shadow-sm hover:shadow-md transition">
                <img src="<?= htmlspecialchars($rp['image_url']) ?>" alt="<?= htmlspecialchars($rp['name']) ?>" class="w-full h-44 object-cover">
                <div class="p-4 space-y-2">
                    <h4 class="font-serif font-bold text-xs text-teak-950 line-clamp-1"><?= htmlspecialchars($rp['name']) ?></h4>
                    <span class="block text-xs font-bold text-teak-900"><?= formatRupiah($rp['price']) ?></span>
                    <a href="produk.php?id=<?= $rp['id'] ?>" class="block text-center bg-teak-50 hover:bg-teak-100 text-teak-900 text-[11px] font-semibold py-1.5 rounded-lg border border-teak-200 transition">Lihat Produk</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
