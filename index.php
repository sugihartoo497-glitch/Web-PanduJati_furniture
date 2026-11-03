<?php
require_once 'config.php';
$page_title = 'Pandu Jati Furniture - Furniture Jati Jepara Kualitas Premium';

// Ambil Produk Unggulan
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_active = 1 AND p.is_featured = 1 ORDER BY p.id DESC LIMIT 6");
$featured_products = $stmt->fetchAll();

// Ambil Kategori
$cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
$categories = $cat_stmt->fetchAll();

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="relative bg-teak-950 text-white overflow-hidden py-20 lg:py-28 wood-texture">
    <div class="absolute inset-0 bg-gradient-to-r from-teak-950 via-teak-950/80 to-transparent z-10"></div>
    <div class="absolute -right-20 top-0 bottom-0 w-2/3 hidden lg:block opacity-45">
        <img src="https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1600&q=80" alt="Furniture Kayu Jati" class="w-full h-full object-cover">
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20">
        <div class="max-w-2xl space-y-6">
            <div class="inline-flex items-center gap-2 bg-amberwood-500/20 border border-amberwood-500/40 text-amberwood-400 px-3.5 py-1.5 rounded-full text-xs font-semibold tracking-wide">
                <i data-lucide="award" class="w-4 h-4"></i> Asli Jepara • Kayu Jati Perhutani Pilihan
            </div>

            <h1 class="font-serif text-3xl sm:text-5xl lg:text-6xl font-bold leading-tight text-white tracking-tight">
                Furniture Jati Berkualitas untuk Rumah Impian Anda
            </h1>

            <p class="text-sm sm:text-base text-teak-200/90 leading-relaxed font-light">
                Temukan furniture kayu jati berkualitas tinggi dari Pandu Jati Furniture. Dibuat langsung di sentra industri mebel Jepara dengan sambungan konstruksi purus-pasak kokoh. Pilih produk siap pesan atau buat furniture custom sesuai kebutuhan Anda.
            </p>

            <div class="flex flex-wrap items-center gap-4 pt-2">
                <a href="katalog.php" class="bg-amberwood-600 hover:bg-amberwood-500 text-white font-semibold px-7 py-3.5 rounded-full text-sm transition shadow-lg shadow-amberwood-700/30 flex items-center gap-2">
                    <i data-lucide="layout-grid" class="w-4 h-4"></i> Lihat Katalog
                </a>
                <a href="custom.php" class="bg-white/10 hover:bg-white/20 text-white border border-white/30 font-semibold px-7 py-3.5 rounded-full text-sm transition backdrop-blur-sm flex items-center gap-2">
                    <i data-lucide="compass" class="w-4 h-4"></i> Pesan Custom
                </a>
            </div>

            <!-- Trust Bar Mini -->
            <div class="pt-8 border-t border-teak-800/80 grid grid-cols-3 gap-4 text-xs text-teak-300">
                <div>
                    <span class="block text-xl font-bold text-amberwood-400 font-serif">100%</span>
                    <span>Jati Perhutani Solid</span>
                </div>
                <div>
                    <span class="block text-xl font-bold text-amberwood-400 font-serif">8 Tahap</span>
                    <span>Tracking Real-Time</span>
                </div>
                <div>
                    <span class="block text-xl font-bold text-amberwood-400 font-serif">Se-Nusantara</span>
                    <span>Kargo Truk Jepara</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trust Section -->
<section class="py-12 bg-white border-b border-teak-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-6 text-center">
            <div class="p-4 rounded-2xl hover:bg-teak-50 transition">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-amber-50 text-amberwood-600 flex items-center justify-center mb-3">
                    <i data-lucide="tree-pine" class="w-6 h-6"></i>
                </div>
                <h4 class="font-bold text-sm text-teak-950 mb-1">Kayu Berkualitas</h4>
                <p class="text-xs text-teak-600">Jati solid TPK oven kering anti rayap</p>
            </div>
            <div class="p-4 rounded-2xl hover:bg-teak-50 transition">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
                    <i data-lucide="sparkles" class="w-6 h-6"></i>
                </div>
                <h4 class="font-bold text-sm text-teak-950 mb-1">Bisa Custom</h4>
                <p class="text-xs text-teak-600">Model, ukuran, &amp; warna finishing</p>
            </div>
            <div class="p-4 rounded-2xl hover:bg-teak-50 transition">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mb-3">
                    <i data-lucide="hammer" class="w-6 h-6"></i>
                </div>
                <h4 class="font-bold text-sm text-teak-950 mb-1">Pengrajin Jepara</h4>
                <p class="text-xs text-teak-600">Konstruksi purus pasak presisi</p>
            </div>
            <div class="p-4 rounded-2xl hover:bg-teak-50 transition">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3">
                    <i data-lucide="truck" class="w-6 h-6"></i>
                </div>
                <h4 class="font-bold text-sm text-teak-950 mb-1">Pengiriman Aman</h4>
                <p class="text-xs text-teak-600">Pintu ke pintu + opsi packing peti</p>
            </div>
            <div class="p-4 rounded-2xl hover:bg-teak-50 transition col-span-2 md:col-span-1">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mb-3">
                    <i data-lucide="shield-check" class="w-6 h-6"></i>
                </div>
                <h4 class="font-bold text-sm text-teak-950 mb-1">Pelayanan Terpercaya</h4>
                <p class="text-xs text-teak-600">Invoice resmi &amp; nomor resi ekspedisi</p>
            </div>
        </div>
    </div>
</section>

<!-- Kategori Grid -->
<section class="py-16 bg-teak-100/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-xl mx-auto mb-10">
            <span class="text-xs font-bold text-amberwood-600 uppercase tracking-widest">Koleksi Pilihan</span>
            <h2 class="font-serif text-2xl sm:text-3xl font-bold text-teak-950 mt-1">Kategori Produk Furniture</h2>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            <?php foreach ($categories as $cat): ?>
            <a href="katalog.php?kategori=<?= urlencode($cat['slug']) ?>" class="bg-white p-5 rounded-2xl border border-teak-200/80 hover:border-amberwood-500 shadow-sm hover:shadow-md transition text-center group">
                <div class="w-12 h-12 mx-auto rounded-full bg-teak-50 flex items-center justify-center mb-3 group-hover:bg-amberwood-50 transition">
                    <i data-lucide="box" class="w-6 h-6 text-teak-700 group-hover:text-amberwood-600 transition"></i>
                </div>
                <h3 class="font-semibold text-xs sm:text-sm text-teak-950 group-hover:text-amberwood-600 transition"><?= htmlspecialchars($cat['name']) ?></h3>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Section Produk Unggulan -->
<section class="py-16 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-12 gap-4">
            <div>
                <span class="text-xs font-bold text-amberwood-600 uppercase tracking-widest">Katalog Terpopuler</span>
                <h2 class="font-serif text-2xl sm:text-4xl font-bold text-teak-950 mt-1">Produk Unggulan Kayu Jati</h2>
            </div>
            <a href="katalog.php" class="text-xs sm:text-sm font-semibold text-teak-700 hover:text-amberwood-600 flex items-center gap-1 transition">
                Lihat Semua Koleksi (<?= count($featured_products) ?>+) <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($featured_products as $p): ?>
            <div class="bg-white rounded-3xl border border-teak-200/80 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col group">
                <!-- Foto -->
                <div class="relative h-64 overflow-hidden bg-teak-100">
                    <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <span class="absolute top-4 left-4 bg-teak-950/80 text-amberwood-400 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider backdrop-blur-sm">
                        <?= htmlspecialchars($p['category_name']) ?>
                    </span>
                    <?php if ($p['original_price'] > $p['price']): ?>
                    <span class="absolute top-4 right-4 bg-rose-600 text-white text-[10px] font-bold px-2.5 py-1 rounded-full">
                        Hemat <?= formatRupiah($p['original_price'] - $p['price']) ?>
                    </span>
                    <?php endif; ?>
                </div>

                <!-- Info -->
                <div class="p-6 flex-grow flex flex-col justify-between">
                    <div>
                        <h3 class="font-serif font-bold text-base text-teak-950 line-clamp-1 group-hover:text-amberwood-600 transition">
                            <?= htmlspecialchars($p['name']) ?>
                        </h3>
                        <p class="text-xs text-teak-600 mt-2 line-clamp-2">
                            <?= htmlspecialchars($p['description']) ?>
                        </p>
                        <div class="mt-3 text-[11px] text-teak-500 flex items-center gap-2">
                            <span class="flex items-center gap-1"><i data-lucide="shield" class="w-3 h-3 text-amberwood-600"></i> Kayu Jati Solid</span>
                            <span>•</span>
                            <span>Stok: <?= $p['stock'] ?> unit</span>
                        </div>
                    </div>

                    <div class="pt-5 mt-4 border-t border-teak-100 flex items-center justify-between">
                        <div>
                            <span class="block text-[10px] text-teak-500 uppercase tracking-wider">Harga Mulai</span>
                            <span class="text-base font-bold text-teak-950 font-serif"><?= formatRupiah($p['price']) ?></span>
                        </div>
                        <a href="produk.php?id=<?= $p['id'] ?>" class="bg-teak-900 hover:bg-amberwood-600 text-white px-4 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 shadow">
                            Lihat Detail <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Spotlight Custom Furniture Banner -->
<section class="py-16 bg-gradient-to-br from-teak-900 to-teak-950 text-white relative overflow-hidden wood-texture">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <div class="space-y-6">
                <div class="inline-flex items-center gap-2 bg-amberwood-500/20 text-amberwood-400 px-3 py-1 rounded-full text-xs font-semibold">
                    <i data-lucide="sparkles" class="w-4 h-4"></i> Layanan Custom Mebel Jepara
                </div>
                <h2 class="font-serif text-3xl sm:text-4xl font-bold leading-tight">
                    Punya Desain Sendiri? <br>Kami Wujudkan dengan Kayu Jati Pilihan
                </h2>
                <p class="text-xs sm:text-sm text-teak-300 leading-relaxed font-light">
                    Kirimkan sketsa, denah ruangan, atau foto inspirasi dari Pinterest/Instagram. Tim pengrajin spesialis kami akan menghitung kebutuhan kayu, estimasi biaya yang transparan, dan membuat gambar teknis sesuai keinginan Anda.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="custom.php" class="bg-amberwood-600 hover:bg-amberwood-500 text-white font-semibold px-6 py-3 rounded-full text-xs sm:text-sm transition flex items-center gap-2 shadow-lg">
                        <i data-lucide="file-text" class="w-4 h-4"></i> Isi Form Request Custom
                    </a>
                    <a href="https://wa.me/<?= $business['whatsapp'] ?>?text=Halo%20Pandu%20Jati,%20saya%20mau%20konsultasi%20desain%20furniture%20custom" target="_blank" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 font-semibold px-6 py-3 rounded-full text-xs sm:text-sm transition flex items-center gap-2">
                        <i data-lucide="message-circle" class="w-4 h-4 text-emerald-400"></i> Diskusi via WhatsApp
                    </a>
                </div>
            </div>
            <div class="relative">
                <div class="rounded-3xl overflow-hidden border-2 border-teak-700 shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1540518614846-7ede433c4570?auto=format&fit=crop&w=800&q=80" alt="Pengrajin Jepara" class="w-full h-80 object-cover">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Cek Pesanan Quick Callout -->
<section class="py-12 bg-teak-100">
    <div class="max-w-4xl mx-auto px-4 text-center space-y-4">
        <h3 class="font-serif text-xl sm:text-2xl font-bold text-teak-950">Sudah Memiliki Pesanan di Pandu Jati?</h3>
        <p class="text-xs sm:text-sm text-teak-600">Lacak perkembangan produksi mebel Anda mulai dari pemilihan bahan kayu, perakitan, finishing, hingga pengiriman truk.</p>
        <form action="cek-pesanan.php" method="GET" class="flex flex-col sm:flex-row gap-3 max-w-lg mx-auto pt-2">
            <input type="text" name="id" placeholder="Masukkan Nomor Pesanan (Contoh: PJF-20261008-0001)" required class="flex-grow px-5 py-3.5 rounded-full border border-teak-300 focus:outline-none focus:ring-2 focus:ring-amberwood-500 text-xs sm:text-sm shadow-inner">
            <button type="submit" class="bg-teak-900 hover:bg-teak-800 text-white font-semibold px-7 py-3.5 rounded-full text-xs sm:text-sm transition flex items-center justify-center gap-2 shadow">
                <i data-lucide="search" class="w-4 h-4 text-amberwood-400"></i> Cek Pesanan
            </button>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
