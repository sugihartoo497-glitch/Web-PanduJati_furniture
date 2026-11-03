<?php
require_once 'config.php';
$page_title = 'Cek & Lacak Status Pesanan - Pandu Jati Furniture';

$search_id = trim($_GET['id'] ?? '');
$order = null;
$items = [];
$history = [];
$not_found = false;

if (!empty($search_id)) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ? LIMIT 1");
    $stmt->execute([$search_id]);
    $order = $stmt->fetch();

    if ($order) {
        $item_stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $item_stmt->execute([$order['id']]);
        $items = $item_stmt->fetchAll();

        $hist_stmt = $pdo->prepare("SELECT * FROM order_status_history WHERE order_id = ? ORDER BY id ASC");
        $hist_stmt->execute([$order['id']]);
        $history = $hist_stmt->fetchAll();
    } else {
        $not_found = true;
    }
}

// 8 Tahap Produksi
$steps = [
    ['key' => 'PENDING', 'label' => 'Pesanan Dibuat', 'desc' => 'Menunggu verifikasi pembayaran / DP'],
    ['key' => 'CONFIRMED', 'label' => 'Pesanan Dikonfirmasi', 'desc' => 'DP terverifikasi, jadwal produksi disusun'],
    ['key' => 'PROCESSING', 'label' => 'Sedang Diproses', 'desc' => 'Pemilihan kayu jati & pengeringan oven'],
    ['key' => 'PRODUCTION', 'label' => 'Sedang Dibuat (Produksi)', 'desc' => 'Konstruksi purus-pasak & pengukiran'],
    ['key' => 'QUALITY_CHECK', 'label' => 'Quality Check', 'desc' => 'Pemeriksaan kehalusan finishing & kekokohan'],
    ['key' => 'READY_TO_SHIP', 'label' => 'Siap Dikirim', 'desc' => 'Packing foam sheet & peti kayu solid'],
    ['key' => 'SHIPPED', 'label' => 'Dalam Pengiriman', 'desc' => 'Diangkut truk ekspedisi mebel Jepara'],
    ['key' => 'COMPLETED', 'label' => 'Pesanan Selesai', 'desc' => 'Mebel diterima pelanggan dengan baik']
];

function getStepIndex($current_status, $steps) {
    if ($current_status === 'CANCELLED') return -1;
    foreach ($steps as $idx => $s) {
        if ($s['key'] === $current_status) {
            return $idx;
        }
    }
    return 0;
}

$current_step_idx = $order ? getStepIndex($order['order_status'], $steps) : 0;

include 'includes/header.php';
?>

<div class="bg-teak-950 text-white py-12 wood-texture">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-3">
        <span class="text-xs font-bold text-amberwood-400 uppercase tracking-widest">Sistem Transparansi Produksi</span>
        <h1 class="font-serif text-3xl sm:text-4xl font-bold">Lacak Status Pesanan Mebel</h1>
        <p class="text-xs sm:text-sm text-teak-300 max-w-xl mx-auto font-light">
            Pantau setiap langkah pengerjaan pesanan Anda dari bengkel kayu Jepara hingga sampai di depan rumah Anda.
        </p>

        <!-- Search Box -->
        <form method="GET" action="cek-pesanan.php" class="max-w-xl mx-auto pt-4 flex flex-col sm:flex-row gap-2">
            <input type="text" name="id" value="<?= htmlspecialchars($search_id) ?>" placeholder="Masukkan nomor pesanan (Contoh: PJF-20261008-0001)" required class="flex-grow px-5 py-3 rounded-full text-teak-950 text-xs sm:text-sm border border-amberwood-400/50 focus:outline-none focus:ring-2 focus:ring-amberwood-400 bg-white">
            <button type="submit" class="bg-amberwood-600 hover:bg-amberwood-500 text-white font-semibold px-7 py-3 rounded-full text-xs sm:text-sm transition flex items-center justify-center gap-2 shadow-lg">
                <i data-lucide="search" class="w-4 h-4"></i> Cek Pesanan
            </button>
        </form>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <?php if ($not_found): ?>
    <div class="bg-white rounded-3xl border border-rose-200 p-8 sm:p-12 text-center space-y-4 max-w-lg mx-auto shadow-sm">
        <div class="w-16 h-16 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center mx-auto">
            <i data-lucide="alert-triangle" class="w-8 h-8"></i>
        </div>
        <h3 class="font-serif text-xl font-bold text-teak-950">Nomor Pesanan Tidak Ditemukan</h3>
        <p class="text-xs text-teak-600">
            Nomor pesanan "<strong><?= htmlspecialchars($search_id) ?></strong>" tidak terdaftar di sistem kami. Silakan periksa kembali huruf besar/kecil atau hubungi customer service kami jika Anda membutuhkan bantuan.
        </p>
        <div class="pt-2">
            <a href="https://wa.me/<?= $business['whatsapp'] ?>?text=Halo%20Pandu%20Jati,%20saya%20mau%20menanyakan%20status%20pesanan%20saya" target="_blank" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-5 py-2.5 rounded-full text-xs font-semibold shadow hover:bg-emerald-700 transition">
                <i data-lucide="message-circle" class="w-4 h-4"></i> Tanya CS di WhatsApp
            </a>
        </div>
    </div>

    <?php elseif ($order): ?>
    <?php 
    $st_badge = getStatusBadge($order['order_status']);
    $pay_badge = getPaymentBadge($order['payment_status']);
    ?>

    <!-- Detail Pesanan Card -->
    <div class="bg-white rounded-3xl border border-teak-200/80 p-6 sm:p-8 shadow-sm space-y-8">
        <!-- Header Info Pesanan -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-teak-100 pb-6">
            <div>
                <span class="text-[10px] text-teak-500 uppercase tracking-widest font-bold">Nomor Pesanan</span>
                <h2 class="font-mono text-2xl font-bold text-teak-950"><?= htmlspecialchars($order['order_number']) ?></h2>
                <span class="text-xs text-teak-500">Dibuat pada: <?= date('d M Y, H:i', strtotime($order['created_at'])) ?> WIB</span>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="px-3.5 py-1.5 rounded-full text-xs font-bold border <?= $st_badge['class'] ?>">
                    <?= $st_badge['label'] ?>
                </span>
                <span class="px-3.5 py-1.5 rounded-full text-xs font-bold border <?= $pay_badge['class'] ?>">
                    <?= $pay_badge['label'] ?>
                </span>
            </div>
        </div>

        <!-- 8-Tahap Progress Timeline Visual -->
        <div>
            <h3 class="font-serif font-bold text-base text-teak-950 mb-6 flex items-center gap-2">
                <i data-lucide="git-commit" class="w-5 h-5 text-amberwood-600"></i>
                Perkembangan Tahapan Produksi
            </h3>

            <div class="relative pl-6 sm:pl-8 space-y-6 before:absolute before:left-3 before:top-2 before:bottom-2 before:w-0.5 before:bg-teak-200">
                <?php foreach ($steps as $idx => $s): ?>
                <?php 
                $is_passed = ($current_step_idx >= $idx);
                $is_current = ($current_step_idx === $idx);
                ?>
                <div class="relative flex items-start gap-4">
                    <!-- Dot Indicator -->
                    <div class="absolute -left-6 sm:-left-8 top-1 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold transition-all <?= $is_current ? 'bg-amberwood-600 text-white ring-4 ring-amberwood-100 ring-offset-1 scale-110' : ($is_passed ? 'bg-emerald-600 text-white' : 'bg-teak-200 text-teak-500') ?>">
                        <?php if ($is_passed && !$is_current): ?>
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        <?php else: ?>
                        <?= $idx + 1 ?>
                        <?php endif; ?>
                    </div>

                    <!-- Step Content -->
                    <div class="bg-teak-50/70 p-4 rounded-2xl border <?= $is_current ? 'border-amberwood-400 bg-amberwood-50/30' : 'border-teak-200/80' ?> w-full transition">
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center">
                            <h4 class="font-serif font-bold text-xs sm:text-sm <?= $is_passed ? 'text-teak-950' : 'text-teak-400' ?>">
                                <?= $s['label'] ?>
                            </h4>
                            <?php if ($is_current): ?>
                            <span class="text-[10px] font-bold text-amberwood-700 bg-amberwood-100 px-2 py-0.5 rounded-full inline-block mt-1 sm:mt-0">
                                Tahap Saat Ini
                            </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-[11px] text-teak-600 mt-1"><?= $s['desc'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Ekspedisi & Catatan Pengrajin -->
        <?php if (!empty($order['tracking_number']) || !empty($order['admin_notes'])): ?>
        <div class="bg-amber-50/60 border border-amber-200 rounded-2xl p-5 space-y-2 text-xs">
            <h4 class="font-serif font-bold text-amber-900 flex items-center gap-1.5">
                <i data-lucide="truck" class="w-4 h-4 text-amber-700"></i> Informasi Ekspedisi &amp; Catatan Workshop
            </h4>
            <?php if (!empty($order['tracking_number'])): ?>
            <p class="text-teak-900">
                <strong>Nomor Resi / Surat Jalan:</strong> 
                <span class="font-mono font-bold text-amber-900 bg-white px-2 py-0.5 rounded border border-amber-300"><?= htmlspecialchars($order['tracking_number']) ?></span>
            </p>
            <?php endif; ?>
            <?php if (!empty($order['admin_notes'])): ?>
            <p class="text-teak-800">
                <strong>Catatan Pengrajin:</strong> <?= htmlspecialchars($order['admin_notes']) ?>
            </p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Rincian Produk Dipesan -->
        <div>
            <h3 class="font-serif font-bold text-base text-teak-950 mb-3 flex items-center gap-2">
                <i data-lucide="shopping-bag" class="w-5 h-5 text-amberwood-600"></i> Rincian Pesanan
            </h3>
            <div class="divide-y divide-teak-100 border border-teak-200 rounded-2xl overflow-hidden text-xs">
                <?php foreach ($items as $it): ?>
                <div class="p-4 bg-white flex justify-between items-center">
                    <div>
                        <h4 class="font-semibold text-teak-950"><?= htmlspecialchars($it['product_name']) ?></h4>
                        <span class="text-teak-500 text-[11px]">Finishing: <?= htmlspecialchars($it['finishing_choice']) ?> • Jumlah: <?= $it['quantity'] ?> unit</span>
                    </div>
                    <span class="font-bold text-teak-900 font-serif"><?= formatRupiah($it['subtotal']) ?></span>
                </div>
                <?php endforeach; ?>
                <div class="p-4 bg-teak-50 flex justify-between items-center text-teak-700">
                    <span>Ongkos Kirim Ekspedisi</span>
                    <span><?= formatRupiah($order['shipping_cost']) ?></span>
                </div>
                <div class="p-4 bg-teak-100/70 flex justify-between items-center text-sm font-bold text-teak-950">
                    <span>Total Pembayaran</span>
                    <span class="font-serif text-base text-amberwood-700"><?= formatRupiah($order['total_amount']) ?></span>
                </div>
            </div>
        </div>

        <!-- Alamat Kirim -->
        <div class="bg-teak-50/50 p-4 rounded-2xl border border-teak-200 text-xs space-y-1">
            <span class="font-bold text-teak-900">Alamat Pengiriman Pelanggan:</span>
            <p class="text-teak-700"><?= htmlspecialchars($order['customer_name']) ?> (<?= htmlspecialchars($order['customer_phone']) ?>)</p>
            <p class="text-teak-600"><?= htmlspecialchars($order['address']) ?>, <?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['province']) ?> <?= htmlspecialchars($order['postal_code']) ?></p>
        </div>

        <!-- Tombol Bantuan WhatsApp -->
        <div class="pt-2 text-center">
            <a href="https://wa.me/<?= $business['whatsapp'] ?>?text=Halo%20Pandu%20Jati,%20saya%20mau%20konfirmasi%20Nomor%20Pesanan%20*<?= urlencode($order['order_number']) ?>*" target="_blank" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-7 py-3 rounded-full text-xs shadow transition">
                <i data-lucide="message-circle" class="w-4 h-4"></i> Konfirmasi Pesanan Ini via WhatsApp
            </a>
        </div>
    </div>

    <?php else: ?>
    <!-- Default state saat belum ada ID -->
    <div class="bg-white rounded-3xl border border-teak-200/80 p-8 sm:p-12 text-center space-y-6 max-w-lg mx-auto shadow-sm">
        <div class="w-16 h-16 bg-teak-100 text-teak-600 rounded-full flex items-center justify-center mx-auto">
            <i data-lucide="search" class="w-8 h-8 text-amberwood-600"></i>
        </div>
        <div class="space-y-2">
            <h3 class="font-serif text-xl font-bold text-teak-950">Masukkan Nomor Pesanan</h3>
            <p class="text-xs text-teak-600 leading-relaxed">
                Silakan ketik nomor pesanan unik yang Anda peroleh saat checkout (contoh: <strong>PJF-20261008-0001</strong>).
            </p>
        </div>
        <div class="pt-2">
            <a href="cek-pesanan.php?id=PJF-20261008-0001" class="text-xs text-amberwood-600 hover:underline font-semibold flex items-center justify-center gap-1">
                <span>Coba Lihat Pesanan Demo (PJF-20261008-0001)</span> <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
