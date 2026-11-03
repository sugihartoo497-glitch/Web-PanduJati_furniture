</main>
<!-- Floating WhatsApp Drawer -->
<div class="fixed bottom-6 right-6 z-40 flex flex-col items-end">
    <!-- Chat Popup Box -->
    <div id="wa-popup" class="hidden mb-4 w-80 bg-white rounded-3xl shadow-2xl border border-teak-200 overflow-hidden transition-all duration-300">
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 text-white p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center font-bold">
                    <i data-lucide="message-square" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h4 class="font-bold text-sm">Customer Service</h4>
                    <p class="text-xs text-emerald-100 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span> Online Workshop
                    </p>
                </div>
            </div>
            <button id="close-wa-btn" class="text-white/80 hover:text-white">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-4 bg-teak-50/50 space-y-3 text-xs">
            <p class="text-teak-700">Halo! Ada yang bisa kami bantu terkait pesanan mebel jati atau konsultasi custom?</p>
            <div class="space-y-1.5">
                <a href="https://wa.me/<?= $business['whatsapp'] ?>?text=Halo%20Pandu%20Jati,%20saya%20mau%20tanya%20produk%20katalog" target="_blank" class="block p-2 rounded-xl bg-white border border-teak-200 hover:border-emerald-500 hover:text-emerald-700 transition">
                    👉 Tanya Stok &amp; Katalog Mebel
                </a>
                <a href="https://wa.me/<?= $business['whatsapp'] ?>?text=Halo%20Pandu%20Jati,%20saya%20tertarik%20konsultasi%20furniture%20custom" target="_blank" class="block p-2 rounded-xl bg-white border border-teak-200 hover:border-emerald-500 hover:text-emerald-700 transition">
                    🪵 Konsultasi Desain &amp; Custom Ukuran
                </a>
                <a href="https://wa.me/<?= $business['whatsapp'] ?>?text=Halo%20Pandu%20Jati,%20saya%20mau%20cek%20estimasi%20ongkir%20ke%20kota%20saya" target="_blank" class="block p-2 rounded-xl bg-white border border-teak-200 hover:border-emerald-500 hover:text-emerald-700 transition">
                    🚚 Cek Ongkir Ekspedisi Truk Jepara
                </a>
            </div>
        </div>
    </div>

    <!-- Toggle Button -->
    <button id="wa-toggle-btn" class="bg-emerald-600 hover:bg-emerald-700 text-white p-4 rounded-full shadow-lg hover:shadow-2xl transition duration-300 flex items-center justify-center group focus:outline-none">
        <i data-lucide="message-circle" class="w-7 h-7 group-hover:scale-110 transition-transform"></i>
    </button>
</div>

<!-- Footer -->
<footer class="bg-teak-950 text-teak-200 pt-16 pb-8 border-t border-teak-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 pb-12 border-b border-teak-900/80">
            <!-- Col 1: About -->
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teak-700 to-teak-900 flex items-center justify-center border border-amberwood-500/30">
                        <i data-lucide="hammer" class="w-5 h-5 text-amberwood-400"></i>
                    </div>
                    <div>
                        <span class="block font-serif text-lg font-bold text-white tracking-wide">PANDU JATI</span>
                        <span class="block text-[10px] tracking-widest text-amberwood-500 uppercase font-semibold">FURNITURE JEPARA</span>
                    </div>
                </div>
                <p class="text-xs text-teak-400 leading-relaxed">
                    Produsen langsung berbagai macam mebel &amp; perabot kayu jati Perhutani berkualitas ekspor di Jepara. Menghadirkan keindahan alami kayu jati solid dengan kekuatan tahan puluhan tahun.
                </p>
                <div class="flex items-center gap-3 pt-2">
                    <a href="https://instagram.com" target="_blank" class="w-8 h-8 rounded-full bg-teak-900 flex items-center justify-center hover:bg-amberwood-600 transition text-white">
                        <i data-lucide="instagram" class="w-4 h-4"></i>
                    </a>
                    <a href="https://tiktok.com" target="_blank" class="w-8 h-8 rounded-full bg-teak-900 flex items-center justify-center hover:bg-amberwood-600 transition text-white">
                        <i data-lucide="video" class="w-4 h-4"></i>
                    </a>
                    <a href="https://wa.me/<?= $business['whatsapp'] ?>" target="_blank" class="w-8 h-8 rounded-full bg-teak-900 flex items-center justify-center hover:bg-emerald-600 transition text-white">
                        <i data-lucide="phone" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            <!-- Col 2: Navigation -->
            <div>
                <h4 class="font-serif font-bold text-white text-sm mb-4 tracking-wider uppercase border-l-2 border-amberwood-500 pl-2">Navigasi Utama</h4>
                <ul class="space-y-2.5 text-xs text-teak-400">
                    <li><a href="index.php" class="hover:text-amberwood-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5"></i> Beranda</a></li>
                    <li><a href="katalog.php" class="hover:text-amberwood-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5"></i> Katalog Furniture Jati</a></li>
                    <li><a href="custom.php" class="hover:text-amberwood-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5"></i> Pesan Custom Furniture</a></li>
                    <li><a href="tentang-kami.php" class="hover:text-amberwood-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5"></i> Tentang Workshop Kami</a></li>
                    <li><a href="cara-pemesanan.php" class="hover:text-amberwood-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5"></i> Prosedur &amp; DP 50%</a></li>
                    <li><a href="cek-pesanan.php" class="hover:text-amberwood-400 transition flex items-center gap-1.5 text-amberwood-300 font-semibold"><i data-lucide="chevron-right" class="w-3.5 h-3.5"></i> Lacak Status Pesanan</a></li>
                </ul>
            </div>

            <!-- Col 3: Workshop -->
            <div>
                <h4 class="font-serif font-bold text-white text-sm mb-4 tracking-wider uppercase border-l-2 border-amberwood-500 pl-2">Workshop &amp; Pabrik</h4>
                <div class="space-y-3 text-xs text-teak-400">
                    <p class="flex items-start gap-2">
                        <i data-lucide="map-pin" class="w-4 h-4 text-amberwood-500 flex-shrink-0 mt-0.5"></i>
                        <span><?= htmlspecialchars($business['address']) ?></span>
                    </p>
                    <p class="flex items-center gap-2">
                        <i data-lucide="clock" class="w-4 h-4 text-amberwood-500 flex-shrink-0"></i>
                        <span><?= htmlspecialchars($business['hours']) ?></span>
                    </p>
                    <p class="flex items-center gap-2 text-emerald-400 font-medium">
                        <i data-lucide="message-circle" class="w-4 h-4 flex-shrink-0"></i>
                        <span>WhatsApp: <?= htmlspecialchars($business['phone']) ?></span>
                    </p>
                </div>
            </div>

            <!-- Col 4: Pembayaran & Pengiriman -->
            <div>
                <h4 class="font-serif font-bold text-white text-sm mb-4 tracking-wider uppercase border-l-2 border-amberwood-500 pl-2">Keamanan Transaksi</h4>
                <p class="text-xs text-teak-400 mb-3">Rekening Resmi Pembayaran DP &amp; Pelunasan:</p>
                <div class="space-y-2 text-xs">
                    <?php foreach ($business['bank_accounts'] as $b): ?>
                    <div class="bg-teak-900/90 border border-teak-800 p-2.5 rounded-xl">
                        <div class="flex justify-between items-center text-white font-semibold">
                            <span>Bank <?= $b['bank'] ?></span>
                            <span class="text-amberwood-400 tracking-wider"><?= $b['number'] ?></span>
                        </div>
                        <span class="text-[10px] text-teak-400">a/n <?= $b['holder'] ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="pt-8 flex flex-col sm:flex-row justify-between items-center text-xs text-teak-500 gap-4">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($business['brand_name']) ?>. Hak Cipta Dilindungi Undang-Undang.</p>
            <div class="flex items-center gap-4">
                <span>Made with pride in Jepara</span>
                <span>•</span>
                <a href="admin.php" class="hover:text-teak-300">Admin Login</a>
            </div>
        </div>
    </div>
</footer>

<script>
    // Initialize Lucide Icons
    lucide.createIcons();

    // Mobile Menu Toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // WA Popup Drawer Toggle
    const waToggleBtn = document.getElementById('wa-toggle-btn');
    const waPopup = document.getElementById('wa-popup');
    const closeWaBtn = document.getElementById('close-wa-btn');
    if (waToggleBtn && waPopup) {
        waToggleBtn.addEventListener('click', () => {
            waPopup.classList.toggle('hidden');
        });
        if (closeWaBtn) {
            closeWaBtn.addEventListener('click', () => {
                waPopup.classList.add('hidden');
            });
        }
    }
</script>
</body>
</html>
