/**
 * ============================================================================
 * JAVASCRIPT: HOMEPAGE (index.html)
 * ============================================================================
 */

document.addEventListener('DOMContentLoaded', async () => {
    PanduApp.init('home');

    const featuredContainer = document.getElementById('featured-products-grid');
    const categoriesContainer = document.getElementById('categories-grid');

    try {
        const res = await PanduApi.getProducts({ featured: 1 });
        if (res.success && res.data) {
            const { products, categories } = res.data;

            // 1. Render Categories Grid
            if (categoriesContainer && categories) {
                categoriesContainer.innerHTML = categories.slice(0, 6).map(cat => `
                    <a href="katalog.html?category=${encodeURIComponent(cat.slug)}" class="bg-white p-5 rounded-2xl border border-teak-200/80 hover:border-amberwood-500 shadow-sm hover:shadow-md transition text-center group">
                        <div class="w-12 h-12 mx-auto rounded-full bg-teak-50 flex items-center justify-center mb-3 group-hover:bg-amberwood-50 transition">
                            <i data-lucide="box" class="w-6 h-6 text-teak-700 group-hover:text-amberwood-600 transition"></i>
                        </div>
                        <h3 class="font-semibold text-xs sm:text-sm text-teak-950 group-hover:text-amberwood-600 transition">${cat.name}</h3>
                    </a>
                `).join('');
            }

            // 2. Render Featured Products
            if (featuredContainer && products) {
                featuredContainer.innerHTML = products.map(p => {
                    const discountHtml = (p.original_price && p.original_price > p.price)
                        ? `<span class="absolute top-4 right-4 bg-rose-600 text-white text-[10px] font-bold px-2.5 py-1 rounded-full">Hemat ${PanduApp.formatRupiah(p.original_price - p.price)}</span>`
                        : '';

                    return `
                        <div class="bg-white rounded-3xl border border-teak-200/80 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col group">
                            <div class="relative h-64 overflow-hidden bg-teak-100">
                                <img src="${p.image_url}" alt="${p.name}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <span class="absolute top-4 left-4 bg-teak-950/80 text-amberwood-400 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider backdrop-blur-sm">
                                    ${p.category_name || 'Mebel Jati'}
                                </span>
                                ${discountHtml}
                            </div>
                            <div class="p-6 flex-grow flex flex-col justify-between">
                                <div>
                                    <h3 class="font-serif font-bold text-base text-teak-950 line-clamp-1 group-hover:text-amberwood-600 transition">
                                        ${p.name}
                                    </h3>
                                    <p class="text-xs text-teak-600 mt-2 line-clamp-2 leading-relaxed">
                                        ${p.description}
                                    </p>
                                    <div class="mt-3 text-[11px] text-teak-500 flex items-center gap-2">
                                        <span class="flex items-center gap-1"><i data-lucide="shield" class="w-3 h-3 text-amberwood-600"></i> ${p.material}</span>
                                        <span>•</span>
                                        <span>Stok: ${p.stock} unit</span>
                                    </div>
                                </div>
                                <div class="pt-5 mt-4 border-t border-teak-100 flex items-center justify-between">
                                    <div>
                                        <span class="block text-[10px] text-teak-500 uppercase tracking-wider">Harga Mulai</span>
                                        <span class="text-base font-bold text-teak-950 font-serif">${PanduApp.formatRupiah(p.price)}</span>
                                    </div>
                                    <a href="produk.html?id=${p.id}" class="bg-teak-900 hover:bg-amberwood-600 text-white px-4 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 shadow">
                                        Lihat Detail <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            }

            if (window.lucide) window.lucide.createIcons();
        }
    } catch (err) {
        console.error('Error loading homepage data:', err);
    }
});
