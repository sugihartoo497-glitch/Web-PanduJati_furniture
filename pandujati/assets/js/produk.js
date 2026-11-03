/**
 * ============================================================================
 * JAVASCRIPT: DETAIL PRODUK (produk.html)
 * ============================================================================
 */

document.addEventListener('DOMContentLoaded', async () => {
    PanduApp.init('katalog');

    const productId = PanduApp.getQueryParam('id');
    const container = document.getElementById('product-detail-container');
    const relatedContainer = document.getElementById('related-products-grid');
    const breadcrumbName = document.getElementById('breadcrumb-product-name');

    if (!productId) {
        window.location.href = 'katalog.html';
        return;
    }

    try {
        const res = await PanduApi.getProductById(productId);
        if (!res.success || !res.data || !res.data.product) {
            if (container) {
                container.innerHTML = `
                    <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-teak-200">
                        <h2 class="text-xl font-bold font-serif text-teak-950">Produk Tidak Ditemukan</h2>
                        <p class="text-xs text-teak-600 mt-2">Mebel yang Anda cari mungkin sudah tidak tersedia.</p>
                        <a href="katalog.html" class="inline-block mt-4 bg-teak-900 text-white px-5 py-2.5 rounded-xl text-xs font-semibold">Kembali ke Katalog</a>
                    </div>
                `;
            }
            return;
        }

        const { product, related } = res.data;

        document.title = `${product.name} - Pandu Jati Furniture`;
        if (breadcrumbName) breadcrumbName.innerText = product.name;

        // Render Detail Produk
        if (container) {
            const discountHtml = (product.original_price && product.original_price > product.price)
                ? `<span class="text-sm text-teak-400 line-through">${PanduApp.formatRupiah(product.original_price)}</span>
                   <span class="bg-rose-100 text-rose-700 text-xs font-semibold px-2 py-0.5 rounded-md">Hemat ${PanduApp.formatRupiah(product.original_price - product.price)}</span>`
                : '';

            const waText = `Halo Pandu Jati Furniture, saya tertarik dengan produk *${product.name}* seharga ${PanduApp.formatRupiah(product.price)}. Apakah stok ini masih tersedia?`;
            const waUrl = `https://wa.me/${PanduApp.config.whatsapp}?text=${encodeURIComponent(waText)}`;

            container.innerHTML = `
                <!-- Gallery Foto (Col 1) -->
                <div class="lg:col-span-6 space-y-4">
                    <div class="rounded-3xl overflow-hidden border border-teak-200 shadow-md bg-teak-100 h-96 sm:h-[460px]">
                        <img src="${product.image_url}" alt="${product.name}" class="w-full h-full object-cover">
                    </div>
                    <div class="grid grid-cols-3 gap-3 text-center text-xs text-teak-600">
                        <div class="bg-white p-3 rounded-2xl border border-teak-200">
                            <i data-lucide="shield-check" class="w-5 h-5 mx-auto text-emerald-600 mb-1"></i>
                            <span>Kayu Jati Legal SVLK</span>
                        </div>
                        <div class="bg-white p-3 rounded-2xl border border-teak-200">
                            <i data-lucide="package" class="w-5 h-5 mx-auto text-blue-600 mb-1"></i>
                            <span>Packing Peti Kayu</span>
                        </div>
                        <div class="bg-white p-3 rounded-2xl border border-teak-200">
                            <i data-lucide="truck" class="w-5 h-5 mx-auto text-amberwood-600 mb-1"></i>
                            <span>Ekspedisi Truk Mebel</span>
                        </div>
                    </div>
                </div>

                <!-- Info & Tombol Pemesanan (Col 2) -->
                <div class="lg:col-span-6 space-y-6">
                    <div>
                        <span class="bg-amberwood-100 text-amberwood-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                            ${product.category_name || 'Furniture Kayu Jati'}
                        </span>
                        <h1 class="font-serif text-2xl sm:text-3xl font-bold text-teak-950 mt-3">
                            ${product.name}
                        </h1>
                        <div class="flex items-center gap-3 mt-4">
                            <span class="text-2xl sm:text-3xl font-bold text-teak-950 font-serif">
                                ${PanduApp.formatRupiah(product.price)}
                            </span>
                            ${discountHtml}
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div class="bg-white p-6 rounded-3xl border border-teak-200/80 shadow-sm space-y-2">
                        <h3 class="font-serif font-bold text-sm text-teak-900 border-b border-teak-100 pb-2">Deskripsi Produk</h3>
                        <p class="text-xs sm:text-sm text-teak-700 leading-relaxed whitespace-pre-line">
                            ${product.description}
                        </p>
                    </div>

                    <!-- Spesifikasi Teknis -->
                    <div class="bg-white p-6 rounded-3xl border border-teak-200/80 shadow-sm space-y-3">
                        <h3 class="font-serif font-bold text-sm text-teak-900 border-b border-teak-100 pb-2">Spesifikasi Mebel</h3>
                        <div class="grid grid-cols-2 gap-y-3 text-xs">
                            <div>
                                <span class="text-teak-500 block">Material Kayu</span>
                                <span class="font-semibold text-teak-900">${product.material}</span>
                            </div>
                            <div>
                                <span class="text-teak-500 block">Dimensi Ukuran</span>
                                <span class="font-semibold text-teak-900">${product.dimensions}</span>
                            </div>
                            <div>
                                <span class="text-teak-500 block">Finishing Warna</span>
                                <span class="font-semibold text-teak-900">${product.finishing}</span>
                            </div>
                            <div>
                                <span class="text-teak-500 block">Estimasi Pembuatan</span>
                                <span class="font-semibold text-teak-900">${product.lead_time}</span>
                            </div>
                            <div>
                                <span class="text-teak-500 block">Estimasi Berat</span>
                                <span class="font-semibold text-teak-900">${product.weight}</span>
                            </div>
                            <div>
                                <span class="text-teak-500 block">Status Ketersediaan</span>
                                <span class="font-semibold text-emerald-600 flex items-center gap-1">
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Ready Stock (${product.stock} unit)
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3 pt-2">
                        <a href="pesanan.html?produk_id=${product.id}" class="w-full bg-teak-900 hover:bg-amberwood-600 text-white font-bold py-4 rounded-2xl text-center transition flex items-center justify-center gap-2 shadow-lg text-sm">
                            <i data-lucide="shopping-cart" class="w-5 h-5"></i> Pesan Produk Ini Secara Online
                        </a>
                        <div class="grid grid-cols-2 gap-3">
                            <a href="${waUrl}" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-2xl text-center transition flex items-center justify-center gap-2 text-xs shadow">
                                <i data-lucide="message-circle" class="w-4 h-4"></i> Chat via WhatsApp
                            </a>
                            <a href="custom.html" class="bg-teak-100 hover:bg-teak-200 text-teak-900 font-semibold py-3 rounded-2xl text-center transition flex items-center justify-center gap-2 text-xs border border-teak-300">
                                <i data-lucide="compass" class="w-4 h-4"></i> Request Custom
                            </a>
                        </div>
                    </div>
                </div>
            `;
        }

        // Render Related Products
        if (relatedContainer && related && related.length > 0) {
            document.getElementById('related-section')?.classList.remove('hidden');
            relatedContainer.innerHTML = related.map(rp => `
                <div class="bg-white rounded-2xl border border-teak-200/80 overflow-hidden shadow-sm hover:shadow-md transition">
                    <img src="${rp.image_url}" alt="${rp.name}" class="w-full h-44 object-cover">
                    <div class="p-4 space-y-2">
                        <h4 class="font-serif font-bold text-xs text-teak-950 line-clamp-1">${rp.name}</h4>
                        <span class="block text-xs font-bold text-teak-900">${PanduApp.formatRupiah(rp.price)}</span>
                        <a href="produk.html?id=${rp.id}" class="block text-center bg-teak-50 hover:bg-teak-100 text-teak-900 text-[11px] font-semibold py-1.5 rounded-lg border border-teak-200 transition">Lihat Produk</a>
                    </div>
                </div>
            `).join('');
        }

        if (window.lucide) window.lucide.createIcons();

    } catch (err) {
        console.error('Error loading product detail:', err);
    }
});
