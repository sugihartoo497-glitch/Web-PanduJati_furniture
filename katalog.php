<?php
require_once 'config.php';
$page_title = 'Katalog Produk Furniture Jati - Pandu Jati Furniture';

// Filter & Search Variables
$search = trim($_GET['q'] ?? '');
$category_slug = trim($_GET['kategori'] ?? 'all');
$sort = trim($_GET['sort'] ?? 'terbaru');
$min_price = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : 0;

// Query Builder
$sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.is_active = 1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category_slug) && $category_slug !== 'all') {
    $sql .= " AND c.slug = ?";
    $params[] = $category_slug;
}

if ($min_price > 0) {
    $sql .= " AND p.price >= ?";
    $params[] = $min_price;
}

if ($max_price > 0) {
    $sql .= " AND p.price <= ?";
    $params[] = $max_price;
}

// Sorting
switch ($sort) {
    case 'termurah':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'termahal':
        $sql .= " ORDER BY p.price DESC";
        break;
    default:
        $sql .= " ORDER BY p.id DESC";
        break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Ambil semua kategori untuk tombol filter
$all_categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();

include 'includes/header.php';
?>

<!-- Header Katalog -->
<div class="bg-teak-950 text-white py-12 wood-texture">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-3">
        <span class="text-xs font-bold text-amberwood-400 uppercase tracking-widest">Katalog Resmi Jepara</span>
        <h1 class="font-serif text-3xl sm:text-4xl font-bold">Koleksi Mebel Kayu Jati Pilihan</h1>
        <p class="text-xs sm:text-sm text-teak-300 max-w-xl mx-auto font-light">
            Jelajahi berbagai pilihan meja, kursi, dipan, lemari, hingga kitchen set berkualitas dari pengrajin Jepara.
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Filter Bar -->
    <div class="bg-white p-5 rounded-3xl border border-teak-200/80 shadow-sm mb-8 space-y-4">
        <form method="GET" action="katalog.php" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            <!-- Search Input -->
            <div class="md:col-span-5 relative">
                <i data-lucide="search" class="w-4 h-4 text-teak-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari mebel jati (contoh: meja makan, dipan)..." class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500 text-xs">
            </div>

            <!-- Kategori Select -->
            <div class="md:col-span-4">
                <select name="kategori" class="w-full px-4 py-2.5 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500 text-xs bg-white">
                    <option value="all">Semua Kategori</option>
                    <?php foreach ($all_categories as $c): ?>
                    <option value="<?= $c['slug'] ?>" <?= $category_slug === $c['slug'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Sorting Select -->
            <div class="md:col-span-2">
                <select name="sort" class="w-full px-4 py-2.5 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500 text-xs bg-white">
                    <option value="terbaru" <?= $sort === 'terbaru' ? 'selected' : '' ?>>Terbaru</option>
                    <option value="termurah" <?= $sort === 'termurah' ? 'selected' : '' ?>>Harga Termurah</option>
                    <option value="termahal" <?= $sort === 'termahal' ? 'selected' : '' ?>>Harga Termahal</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div class="md:col-span-1">
                <button type="submit" class="w-full bg-teak-900 hover:bg-amberwood-600 text-white py-2.5 rounded-2xl text-xs font-semibold transition flex items-center justify-center gap-1">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i> Filter
                </button>
            </div>
        </form>

        <!-- Category Pills Shortcut -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs no-scrollbar pt-2 border-t border-teak-100">
            <a href="katalog.php" class="px-4 py-1.5 rounded-full border transition whitespace-nowrap <?= $category_slug === 'all' ? 'bg-teak-900 text-amberwood-400 border-teak-900 font-semibold' : 'bg-teak-50 text-teak-700 border-teak-200 hover:bg-teak-100' ?>">
                Semua
            </a>
            <?php foreach ($all_categories as $c): ?>
            <a href="katalog.php?kategori=<?= urlencode($c['slug']) ?>" class="px-4 py-1.5 rounded-full border transition whitespace-nowrap <?= $category_slug === $c['slug'] ? 'bg-teak-900 text-amberwood-400 border-teak-900 font-semibold' : 'bg-teak-50 text-teak-700 border-teak-200 hover:bg-teak-100' ?>">
                <?= htmlspecialchars($c['name']) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Hasil Produk -->
    <div class="mb-4 flex justify-between items-center text-xs text-teak-600">
        <span>Menampilkan <strong><?= count($products) ?></strong> produk mebel</span>
        <?php if (!empty($search) || $category_slug !== 'all'): ?>
        <a href="katalog.php" class="text-rose-600 hover:underline flex items-center gap-1">
            <i data-lucide="x" class="w-3 h-3"></i> Reset Filter
        </a>
        <?php endif; ?>
    </div>

    <?php if (empty($products)): ?>
    <div class="bg-white rounded-3xl border border-teak-200 p-12 text-center space-y-4 max-w-lg mx-auto">
        <div class="w-16 h-16 bg-teak-100 rounded-full flex items-center justify-center mx-auto text-teak-500">
            <i data-lucide="package-x" class="w-8 h-8"></i>
        </div>
        <h3 class="font-serif text-xl font-bold text-teak-950">Produk Tidak Ditemukan</h3>
        <p class="text-xs text-teak-600">Tidak ada produk yang cocok dengan kata kunci atau filter pencarian Anda. Anda juga bisa membuat mebel impian Anda via custom order.</p>
        <div class="pt-2 flex justify-center gap-3">
            <a href="katalog.php" class="bg-teak-900 text-white px-5 py-2.5 rounded-xl text-xs font-semibold">Lihat Semua Produk</a>
            <a href="custom.php" class="bg-amberwood-600 text-white px-5 py-2.5 rounded-xl text-xs font-semibold">Pesan Custom</a>
        </div>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($products as $p): ?>
        <div class="bg-white rounded-3xl border border-teak-200/80 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col group">
            <div class="relative h-56 bg-teak-100 overflow-hidden">
                <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <span class="absolute top-3 left-3 bg-teak-950/80 text-amberwood-400 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider backdrop-blur-sm">
                    <?= htmlspecialchars($p['category_name']) ?>
                </span>
                <?php if ($p['original_price'] > $p['price']): ?>
                <span class="absolute top-3 right-3 bg-rose-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                    Diskon
                </span>
                <?php endif; ?>
            </div>
            
            <div class="p-5 flex-grow flex flex-col justify-between">
                <div>
                    <h3 class="font-serif font-bold text-sm text-teak-950 line-clamp-2 group-hover:text-amberwood-600 transition">
                        <?= htmlspecialchars($p['name']) ?>
                    </h3>
                    <p class="text-[11px] text-teak-500 mt-1.5 line-clamp-2">
                        <?= htmlspecialchars($p['description']) ?>
                    </p>
                </div>

                <div class="pt-4 mt-3 border-t border-teak-100 flex items-center justify-between">
                    <div>
                        <span class="block text-[9px] text-teak-400 uppercase">Mulai dari</span>
                        <span class="text-sm font-bold text-teak-950 font-serif"><?= formatRupiah($p['price']) ?></span>
                    </div>
                    <a href="produk.php?id=<?= $p['id'] ?>" class="bg-teak-900 hover:bg-amberwood-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition flex items-center gap-1 shadow">
                        Detail <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
