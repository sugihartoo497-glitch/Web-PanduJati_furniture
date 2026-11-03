/**
 * ============================================================================
 * JAVASCRIPT GLOBAL APP: PANDU JATI FURNITURE
 * ============================================================================
 * Mengatur fungsi-fungsi antarmuka umum: navigasi, footer, formatting rupiah,
 * badge status, dan floating WhatsApp.
 */

(function (window) {
    'use strict';

    const PanduApp = {
        config: {
            brandName: 'Pandu Jati Furniture',
            phone: '+62 851-9123-5955',
            whatsapp: '6285191235955',
            email: 'info@pandujatifurniture.com',
            address: 'Jl. Raya Tahunan - Batealit KM 3.5, Sentra Industri Mebel Kayu Jati, Jepara, Jawa Tengah 59427'
        },

        // Format angka ke format mata uang Rupiah (contoh: Rp 8.500.000)
        formatRupiah(amount) {
            const num = parseFloat(amount) || 0;
            return 'Rp ' + num.toLocaleString('id-ID');
        },

        // Ambil query parameter dari URL browser
        getQueryParam(key) {
            const params = new URLSearchParams(window.location.search);
            return params.get(key) || '';
        },

        // Dapatkan style & label badge status pesanan
        getStatusBadge(status) {
            const map = {
                'PENDING':       { label: 'Pesanan Dibuat',       class: 'bg-amber-100 text-amber-800 border-amber-300' },
                'CONFIRMED':     { label: 'Pesanan Dikonfirmasi', class: 'bg-blue-100 text-blue-800 border-blue-300' },
                'PROCESSING':    { label: 'Sedang Diproses',      class: 'bg-indigo-100 text-indigo-800 border-indigo-300' },
                'PRODUCTION':    { label: 'Sedang Dibuat',        class: 'bg-orange-100 text-orange-800 border-orange-300' },
                'QUALITY_CHECK': { label: 'Quality Check',        class: 'bg-purple-100 text-purple-800 border-purple-300' },
                'READY_TO_SHIP': { label: 'Siap Dikirim',         class: 'bg-cyan-100 text-cyan-800 border-cyan-300' },
                'SHIPPED':       { label: 'Dalam Pengiriman',     class: 'bg-sky-100 text-sky-800 border-sky-300' },
                'COMPLETED':     { label: 'Pesanan Selesai',      class: 'bg-emerald-100 text-emerald-800 border-emerald-300' },
                'CANCELLED':     { label: 'Dibatalkan',           class: 'bg-rose-100 text-rose-800 border-rose-300' }
            };
            return map[status] || { label: status, class: 'bg-stone-100 text-stone-800 border-stone-300' };
        },

        // Dapatkan style & label badge status pembayaran
        getPaymentBadge(status) {
            const map = {
                'UNPAID':   { label: 'Belum Dibayar', class: 'bg-rose-100 text-rose-800 border-rose-300' },
                'DP':       { label: 'DP 50% Masuk', class: 'bg-blue-100 text-blue-800 border-blue-300' },
                'PAID':     { label: 'Lunas',         class: 'bg-emerald-100 text-emerald-800 border-emerald-300' },
                'REFUNDED': { label: 'Refund',        class: 'bg-stone-100 text-stone-800 border-stone-300' }
            };
            return map[status] || { label: status, class: 'bg-stone-100 text-stone-800 border-stone-300' };
        },

        // Render Navbar Global
        renderNavbar(activePage = '') {
            const headerEl = document.getElementById('main-header');
            if (!headerEl) return;

            const isCurrent = (page) => activePage === page ? 'text-amberwood-600 font-semibold' : 'text-teak-800 hover:text-amberwood-600';

            headerEl.innerHTML = `
                <!-- Top Bar -->
                <div class="bg-teak-950 text-teak-200 text-xs py-2 px-4 border-b border-teak-900">
                    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
                        <div class="flex items-center gap-4 text-center sm:text-left">
                            <span class="flex items-center gap-1.5"><i data-lucide="map-pin" class="w-3.5 h-3.5 text-amberwood-500"></i> Sentra Mebel Jepara, Jawa Tengah</span>
                            <span class="hidden md:inline text-teak-700">•</span>
                            <span class="hidden md:flex items-center gap-1.5"><i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-400"></i> Kayu Jati Legal SVLK Perhutani</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <a href="https://wa.me/${this.config.whatsapp}" target="_blank" class="hover:text-white transition flex items-center gap-1 text-emerald-400 font-medium">
                                <i data-lucide="phone" class="w-3.5 h-3.5"></i> ${this.config.phone}
                            </a>
                            <span class="text-teak-700">•</span>
                            <a href="admin.html" class="hover:text-amberwood-400 transition flex items-center gap-1">
                                <i data-lucide="lock" class="w-3 h-3"></i> Admin Portal
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Main Navbar -->
                <div class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-teak-200/60 shadow-sm">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between items-center h-20">
                            <!-- Logo Brand -->
                            <a href="index.html" class="flex items-center gap-3 group">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-teak-800 to-teak-950 flex items-center justify-center shadow-md shadow-teak-900/20 group-hover:scale-105 transition-transform">
                                    <i data-lucide="hammer" class="w-6 h-6 text-amberwood-500"></i>
                                </div>
                                <div>
                                    <span class="block font-serif text-xl sm:text-2xl font-bold tracking-tight text-teak-950 group-hover:text-teak-700 transition">
                                        PANDU JATI
                                    </span>
                                    <span class="block text-[10px] sm:text-xs font-semibold tracking-widest text-amberwood-600 uppercase">
                                        FURNITURE JEPARA
                                    </span>
                                </div>
                            </a>

                            <!-- Desktop Menu -->
                            <nav class="hidden lg:flex items-center gap-7 text-sm font-medium">
                                <a href="index.html" class="transition ${isCurrent('home')}">Beranda</a>
                                <a href="katalog.html" class="transition ${isCurrent('katalog')}">Katalog Produk</a>
                                <a href="custom.html" class="transition ${isCurrent('custom')}">Furniture Custom</a>
                                <a href="tentang-kami.html" class="transition ${isCurrent('tentang')}">Tentang Kami</a>
                                <a href="cara-pemesanan.html" class="transition ${isCurrent('cara-pesan')}">Cara Pesan</a>
                                <a href="cek-pesanan.html" class="transition flex items-center gap-1.5 ${isCurrent('cek-pesanan')}">
                                    <i data-lucide="search" class="w-4 h-4 text-amberwood-600"></i> Cek Pesanan
                                </a>
                                <a href="kontak.html" class="transition ${isCurrent('kontak')}">Kontak</a>
                            </nav>

                            <!-- CTA Button -->
                            <div class="hidden sm:flex items-center gap-3">
                                <a href="katalog.html" class="bg-teak-900 hover:bg-teak-800 text-amberwood-400 border border-amberwood-600/40 px-5 py-2.5 rounded-full text-xs font-semibold tracking-wide transition shadow-sm hover:shadow flex items-center gap-2">
                                    <i data-lucide="shopping-bag" class="w-4 h-4"></i> Pesan Sekarang
                                </a>
                            </div>

                            <!-- Mobile Menu Button -->
                            <button id="mobile-menu-btn" class="lg:hidden p-2 text-teak-800 hover:text-amberwood-600 rounded-lg hover:bg-teak-100 transition" aria-label="Buka Menu">
                                <i data-lucide="menu" class="w-6 h-6"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Mobile Drawer -->
                    <div id="mobile-menu" class="hidden lg:hidden bg-white border-b border-teak-200 px-6 py-6 shadow-xl space-y-4">
                        <a href="index.html" class="block text-base font-semibold text-teak-900 py-2 border-b border-teak-100">Beranda</a>
                        <a href="katalog.html" class="block text-base font-medium text-teak-800 py-2 border-b border-teak-100">Katalog Produk</a>
                        <a href="custom.html" class="block text-base font-medium text-teak-800 py-2 border-b border-teak-100">Furniture Custom</a>
                        <a href="tentang-kami.html" class="block text-base font-medium text-teak-800 py-2 border-b border-teak-100">Tentang Kami</a>
                        <a href="cara-pemesanan.html" class="block text-base font-medium text-teak-800 py-2 border-b border-teak-100">Cara Pemesanan</a>
                        <a href="cek-pesanan.html" class="block text-base font-semibold text-amberwood-700 py-2 border-b border-teak-100 flex items-center gap-2">
                            <i data-lucide="search" class="w-4 h-4"></i> Cek Status Pesanan
                        </a>
                        <a href="kontak.html" class="block text-base font-medium text-teak-800 py-2 border-b border-teak-100">Kontak Workshop</a>
                        <div class="pt-2">
                            <a href="https://wa.me/${this.config.whatsapp}" target="_blank" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-semibold flex items-center justify-center gap-2 text-sm shadow">
                                <i data-lucide="message-circle" class="w-4 h-4"></i> Konsultasi WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            `;

            // Setup hamburger event
            const btn = document.getElementById('mobile-menu-btn');
            const drawer = document.getElementById('mobile-menu');
            if (btn && drawer) {
                btn.addEventListener('click', () => {
                    drawer.classList.toggle('hidden');
                });
            }
        },

        // Render Footer Global & Floating WhatsApp
        renderFooter() {
            const footerEl = document.getElementById('main-footer');
            if (!footerEl) return;

            footerEl.innerHTML = `
                <!-- Floating WhatsApp Widget -->
                <div class="fixed bottom-6 right-6 z-40 flex flex-col items-end">
                    <div id="wa-popup" class="hidden mb-4 w-80 bg-white rounded-3xl shadow-2xl border border-teak-200 overflow-hidden transition-all duration-300">
                        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 text-white p-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center font-bold">
                                    <i data-lucide="message-square" class="w-5 h-5 text-white"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-sm">Customer Service</h4>
                                    <p class="text-xs text-emerald-100 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span> Workshop Online
                                    </p>
                                </div>
                            </div>
                            <button id="close-wa-btn" class="text-white/80 hover:text-white">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>
                        <div class="p-4 bg-teak-50/50 space-y-2 text-xs">
                            <p class="text-teak-700 mb-2">Halo! Ada yang bisa kami bantu seputar mebel jati Jepara?</p>
                            <a href="https://wa.me/${this.config.whatsapp}?text=Halo%20Pandu%20Jati,%20saya%20mau%20tanya%20produk%20katalog" target="_blank" class="block p-2 rounded-xl bg-white border border-teak-200 hover:border-emerald-500 hover:text-emerald-700 transition">
                                👉 Tanya Stok &amp; Katalog Mebel
                            </a>
                            <a href="https://wa.me/${this.config.whatsapp}?text=Halo%20Pandu%20Jati,%20saya%20tertarik%20konsultasi%20furniture%20custom" target="_blank" class="block p-2 rounded-xl bg-white border border-teak-200 hover:border-emerald-500 hover:text-emerald-700 transition">
                                🪵 Konsultasi Desain &amp; Custom Ukuran
                            </a>
                            <a href="https://wa.me/${this.config.whatsapp}?text=Halo%20Pandu%20Jati,%20saya%20mau%20cek%20estimasi%20ongkir%20ke%20kota%20saya" target="_blank" class="block p-2 rounded-xl bg-white border border-teak-200 hover:border-emerald-500 hover:text-emerald-700 transition">
                                🚚 Cek Ongkir Ekspedisi Truk Jepara
                            </a>
                        </div>
                    </div>

                    <button id="wa-toggle-btn" class="bg-emerald-600 hover:bg-emerald-700 text-white p-4 rounded-full shadow-lg hover:shadow-2xl transition duration-300 flex items-center justify-center group focus:outline-none" aria-label="Chat WhatsApp">
                        <i data-lucide="message-circle" class="w-7 h-7 group-hover:scale-110 transition-transform"></i>
                    </button>
                </div>

                <!-- Footer Body -->
                <footer class="bg-teak-950 text-teak-200 pt-16 pb-8 border-t border-teak-900">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 pb-12 border-b border-teak-900/80">
                            <!-- Col 1 -->
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
                                    Produsen langsung mebel kayu jati perhutani berkualitas ekspor di Jepara. Menghadirkan keindahan alami kayu solid dengan konstruksi kokoh tahan puluhan tahun.
                                </p>
                            </div>

                            <!-- Col 2 -->
                            <div>
                                <h4 class="font-serif font-bold text-white text-sm mb-4 tracking-wider uppercase border-l-2 border-amberwood-500 pl-2">Navigasi Utama</h4>
                                <ul class="space-y-2 text-xs text-teak-400">
                                    <li><a href="index.html" class="hover:text-amberwood-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5"></i> Beranda</a></li>
                                    <li><a href="katalog.html" class="hover:text-amberwood-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5"></i> Katalog Furniture Jati</a></li>
                                    <li><a href="custom.html" class="hover:text-amberwood-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5"></i> Pesan Custom Furniture</a></li>
                                    <li><a href="tentang-kami.html" class="hover:text-amberwood-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5"></i> Tentang Workshop Kami</a></li>
                                    <li><a href="cara-pemesanan.html" class="hover:text-amberwood-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5"></i> Prosedur &amp; DP 50%</a></li>
                                    <li><a href="cek-pesanan.html" class="hover:text-amberwood-400 transition flex items-center gap-1.5 text-amberwood-300 font-semibold"><i data-lucide="chevron-right" class="w-3.5 h-3.5"></i> Lacak Status Pesanan</a></li>
                                </ul>
                            </div>

                            <!-- Col 3 -->
                            <div>
                                <h4 class="font-serif font-bold text-white text-sm mb-4 tracking-wider uppercase border-l-2 border-amberwood-500 pl-2">Workshop &amp; Pabrik</h4>
                                <div class="space-y-3 text-xs text-teak-400">
                                    <p class="flex items-start gap-2">
                                        <i data-lucide="map-pin" class="w-4 h-4 text-amberwood-500 flex-shrink-0 mt-0.5"></i>
                                        <span>${this.config.address}</span>
                                    </p>
                                    <p class="flex items-center gap-2 text-emerald-400 font-medium">
                                        <i data-lucide="message-circle" class="w-4 h-4 flex-shrink-0"></i>
                                        <span>WhatsApp: ${this.config.phone}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Col 4 -->
                            <div>
                                <h4 class="font-serif font-bold text-white text-sm mb-4 tracking-wider uppercase border-l-2 border-amberwood-500 pl-2">Keamanan Transaksi</h4>
                                <p class="text-xs text-teak-400 mb-3">Rekening Resmi Pembayaran DP:</p>
                                <div class="space-y-2 text-xs">
                                    <div class="bg-teak-900 border border-teak-800 p-2.5 rounded-xl">
                                        <div class="flex justify-between items-center text-white font-semibold">
                                            <span>Bank BCA</span>
                                            <span class="text-amberwood-400 tracking-wider">1234-567-890</span>
                                        </div>
                                        <span class="text-[10px] text-teak-400">a/n Pandu Jati Furniture</span>
                                    </div>
                                    <div class="bg-teak-900 border border-teak-800 p-2.5 rounded-xl">
                                        <div class="flex justify-between items-center text-white font-semibold">
                                            <span>Bank Mandiri</span>
                                            <span class="text-amberwood-400 tracking-wider">138-00-1234567-8</span>
                                        </div>
                                        <span class="text-[10px] text-teak-400">a/n Pandu Jati Furniture</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-8 flex flex-col sm:flex-row justify-between items-center text-xs text-teak-500 gap-4">
                            <p>&copy; ${new Date().getFullYear()} ${this.config.brandName}. Hak Cipta Dilindungi Undang-Undang.</p>
                            <div class="flex items-center gap-4">
                                <span>Made with pride in Jepara</span>
                                <span>•</span>
                                <a href="admin.html" class="hover:text-teak-300">Admin Login</a>
                            </div>
                        </div>
                    </div>
                </footer>
            `;

            // WA popup toggle
            const waBtn = document.getElementById('wa-toggle-btn');
            const waPopup = document.getElementById('wa-popup');
            const closeBtn = document.getElementById('close-wa-btn');
            if (waBtn && waPopup) {
                waBtn.addEventListener('click', () => waPopup.classList.toggle('hidden'));
                if (closeBtn) closeBtn.addEventListener('click', () => waPopup.classList.add('hidden'));
            }
        },

        // Inisialisasi awal seluruh halaman
        init(activePage = '') {
            this.renderNavbar(activePage);
            this.renderFooter();
            if (window.lucide) {
                window.lucide.createIcons();
            }
        }
    };

    window.PanduApp = PanduApp;

})(window);
