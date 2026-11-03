<?php
require_once 'config.php';
$page_title = 'Cara Pemesanan & Pembayaran - Pandu Jati Furniture';
include 'includes/header.php';
?>

<div class="bg-teak-950 text-white py-14 wood-texture">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-3">
        <span class="text-xs font-bold text-amberwood-400 uppercase tracking-widest">Panduan Pembelian Aman</span>
        <h1 class="font-serif text-3xl sm:text-5xl font-bold">Cara Pemesanan Mebel Online</h1>
        <p class="text-xs sm:text-sm text-teak-300 max-w-xl mx-auto font-light">
            Alur belanja mebel yang jelas, transparan, dan aman bagi pelanggan di seluruh wilayah Indonesia.
        </p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-14 space-y-12">
    <!-- 6 Langkah Pemesanan -->
    <div class="space-y-6">
        <h2 class="font-serif text-2xl font-bold text-teak-950 border-b border-teak-200 pb-3">
            6 Langkah Belanja Furniture di Pandu Jati
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-3xl border border-teak-200/80 shadow-sm space-y-2">
                <span class="w-8 h-8 rounded-full bg-amberwood-100 text-amberwood-800 font-bold flex items-center justify-center text-xs">1</span>
                <h3 class="font-bold text-sm text-teak-950">Pilih Produk atau Custom Desain</h3>
                <p class="text-xs text-teak-600">Pilih dari katalog siap pesan kami atau kirimkan rancangan custom ukuran ruangan Anda melalui form Custom.</p>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-teak-200/80 shadow-sm space-y-2">
                <span class="w-8 h-8 rounded-full bg-amberwood-100 text-amberwood-800 font-bold flex items-center justify-center text-xs">2</span>
                <h3 class="font-bold text-sm text-teak-950">Isi Form &amp; Dapatkan Order ID</h3>
                <p class="text-xs text-teak-600">Isi data alamat pengiriman lengkap. Sistem secara otomatis menerbitkan Nomor Pesanan unik (contoh: PJF-20261008-0001).</p>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-teak-200/80 shadow-sm space-y-2">
                <span class="w-8 h-8 rounded-full bg-amberwood-100 text-amberwood-800 font-bold flex items-center justify-center text-xs">3</span>
                <h3 class="font-bold text-sm text-teak-950">Pembayaran DP 50% Produksi</h3>
                <p class="text-xs text-teak-600">Lakukan pembayaran uang muka (DP) 50% ke salah satu rekening resmi kami untuk memulai pengerjaan kayu di bengkel.</p>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-teak-200/80 shadow-sm space-y-2">
                <span class="w-8 h-8 rounded-full bg-amberwood-100 text-amberwood-800 font-bold flex items-center justify-center text-xs">4</span>
                <h3 class="font-bold text-sm text-teak-950">Lacak Perkembangan di Website</h3>
                <p class="text-xs text-teak-600">Pelanggan dapat memantau 8 tahap pengerjaan (kayu, rakit, finishing, QC) secara mandiri lewat halaman "Cek Pesanan".</p>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-teak-200/80 shadow-sm space-y-2">
                <span class="w-8 h-8 rounded-full bg-amberwood-100 text-amberwood-800 font-bold flex items-center justify-center text-xs">5</span>
                <h3 class="font-bold text-sm text-teak-950">Pelunasan &amp; Foto Hasil Akhir</h3>
                <p class="text-xs text-teak-600">Sebelum barang dinaikkan ke truk ekspedisi, kami mengirimkan dokumentasi foto dan video hasil akhir mebel yang telah lulus QC.</p>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-teak-200/80 shadow-sm space-y-2">
                <span class="w-8 h-8 rounded-full bg-amberwood-100 text-amberwood-800 font-bold flex items-center justify-center text-xs">6</span>
                <h3 class="font-bold text-sm text-teak-950">Pengiriman Kargo Truk Jepara</h3>
                <p class="text-xs text-teak-600">Pesanan diantar langsung oleh supir kargo mebel pintu-ke-pintu dengan nomor resi dan kontak supir yang bisa dihubungi.</p>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="bg-teak-900 text-white rounded-3xl p-8 text-center space-y-4">
        <h3 class="font-serif text-xl sm:text-2xl font-bold">Siap Mengisi Rumah Impian Anda?</h3>
        <p class="text-xs sm:text-sm text-teak-300 max-w-md mx-auto">Konsultasikan segera furniture idaman Anda dengan tim spesialis pengrajin Jepara.</p>
        <div class="pt-2 flex justify-center gap-3">
            <a href="katalog.php" class="bg-amberwood-600 hover:bg-amberwood-500 text-white px-6 py-3 rounded-full text-xs font-semibold shadow">Buka Katalog Produk</a>
            <a href="pesanan.php" class="bg-white/10 hover:bg-white/20 text-white px-6 py-3 rounded-full text-xs font-semibold border border-white/20">Mulai Pemesanan</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
