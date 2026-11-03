/**
 * ============================================================================
 * JAVASCRIPT: KATALOG PRODUK (katalog.html)
 * ============================================================================
 */

document.addEventListener('DOMContentLoaded', async () => {
    PanduApp.init('katalog');

    const searchInput = document.getElementById('search-input');
    const categorySelect = document.getElementById('category-select');
    const sortSelect = document.getElementById('sort-select');
    const filterForm = document.getElementById('filter-form');
    const productsContainer = document.getElementById('products-grid');
    const totalCountEl = document.getElementById('total-count');
    const categoryPillsContainer = document.getElementById('category-pills');

    // Ambil param awal dari URL
    const initialCategory = PanduApp.getQueryParam('category') || 'all';
    const initialSearch = PanduApp.getQueryParam('search') || '';
    const initialSort = PanduApp.getQueryParam('sort') || 'terbaru';

    if (searchInput) searchInput.value = initialSearch;
    if (sortSelect) sortSelect.value = initialSort;

    async function loadProducts() {
        if (!productsContainer) return;
        productsContainer.innerHTML = `
            <div class="col-span-full py-16 text-center text-teak-500">
                <i data-lucide="loader-2" class="w-8 h-8 animate-spin mx-auto mb-2 text-amberwood-600"></i>
                <p class="text-xs">Memuat katalog mebel...</p>
            </div>
        `;
        if (window.lucide) window.lucide.createIcons();

        const params = {
            search: searchInput ? searchInput.value.trim() : '',
            category: categorySelect ? categorySelect.value : 'all',
            sort: sortSelect ? sortSelect.value : 'terbaru'
        };

        const res = await PanduApi.getProducts(params);
        if (!res.success || !res.data) {
            productsContainer.innerHTML = `<div class="col-span-full py-8 text-center text-rose-600 text-xs">${res.message || 'Gagal memuat produk.'}</div>`;
            return;
        }

        const { products, categories, total } = res.data;
        if (totalCountEl) totalCountEl.innerText = total;

        // Render Category Select Options & Category Pills (hanya sekali jika belum terisi)
        if (categorySelect && categorySelect.options.length <= 1 && categories) {
            categories.forEach(cat => {
                const opt = document.createElement('option');
                opt.value = cat.slug;
                opt.textContent = cat.name;
                if (cat.slug === initialCategory) opt.selected = true;
                categorySelect.appendChild(opt);
            });
        }

        if (categoryPillsContainer && categoryPillsContainer.children.length === 0 && categories) {
            const currentCat = categorySelect ? categorySelect.value : 'all';
            let pillsHtml = `
                <button type="button" data-cat="all" class="cat-pill px-4 py-1.5 rounded-full border text-xs transition whitespace-nowrap ${currentCat === 'all' ? 'bg-teak-900 text-amberwood-400 border-teak-900 font-semibold' : 'bg-teak-50 text-teak-700 border-teak-200 hover:bg-teak-100'}">
                    Semua
                </button>
            `;
            pillsHtml += categories.map(c => `
                <button type="button" data-cat="${c.slug}" class="cat-pill px-4 py-1.5 rounded-full border text-xs transition whitespace-nowrap ${currentCat === c.slug ? 'bg-teak-900 text-amberwood-400 border-teak-900 font-semibold' : 'bg-teak-50 text-teak-700 border-teak-200 hover:bg-teak-100'}">
                    ${c.name}
                </button>
            `).join('');
            categoryPillsContainer.innerHTML = pillsHtml;

            // Pill click handler
            categoryPillsContainer.querySelectorAll('.cat-pill').forEach(btn => {
                btn.addEventListener('click', () => {
                    const chosen = btn.getAttribute('data-cat');
                    if (categorySelect) categorySelect.value = chosen;
                    categoryPillsContainer.querySelectorAll('.cat-pill').forEach(b => {
                        b.className = `cat-pill px-4 py-1.5 rounded-full border text-xs transition whitespace-nowrap ${b.getAttribute('data-cat') === chosen ? 'bg-teak-900 text-amberwood-400 border-teak-900 font-semibold' : 'bg-teak-50 text-teak-700 border-teak-200 hover:bg-teak-100'}`;
                    });
                    loadProducts();
                });
            });
        }

        // Render Products
        if (products.length === 0) {
            productsContainer.innerHTML = `
                <div class="col-span-full bg-white rounded-3xl border border-teak-200 p-12 text-center space-y-4 max-w-lg mx-auto">
                    <div class="w-16 h-16 bg-teak-100 rounded-full flex items-center justify-center mx-auto text-teak-500">
                        <i data-lucide="package-x" class="w-8 h-8"></i>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-teak-950">Produk Tidak Ditemukan</h3>
                    <p class="text-xs text-teak-600">Tidak ada mebel yang cocok dengan filter Anda. Anda juga bisa membuat pesanan custom sesuai model impian Anda.</p>
                    <div class="pt-2 flex justify-center gap-3">
                        <button type="button" id="reset-filter-btn" class="bg-teak-900 text-white px-5 py-2.5 rounded-xl text-xs font-semibold">Reset Pencarian</button>
                        <a href="custom.html" class="bg-amberwood-600 text-white px-5 py-2.5 rounded-xl text-xs font-semibold">Pesan Custom</a>
                    </div>
                </div>
            `;
            const rst = document.getElementById('reset-filter-btn');
            if (rst) {
                rst.addEventListener('click', () => {
                    if (searchInput) searchInput.value = '';
                    if (categorySelect) categorySelect.value = 'all';
                    if (sortSelect) sortSelect.value = 'terbaru';
                    loadProducts();
                });
            }
        } else {
            productsContainer.innerHTML = products.map(p => {
                const discount = (p.original_price && p.original_price > p.price)
                    ? `<span class="absolute top-3 right-3 bg-rose-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">Diskon</span>`
                    : '';

                return `
                    <div class="bg-white rounded-3xl border border-teak-200/80 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col group">
                        <div class="relative h-56 bg-teak-100 overflow-hidden">
                            <img src="${p.image_url}" alt="${p.name}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <span class="absolute top-3 left-3 bg-teak-950/80 text-amberwood-400 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider backdrop-blur-sm">
                                ${p.category_name || 'Jati Solid'}
                            </span>
                            ${discount}
                        </div>
                        <div class="p-5 flex-grow flex flex-col justify-between">
                            <div>
                                <h3 class="font-serif font-bold text-sm text-teak-950 line-clamp-2 group-hover:text-amberwood-600 transition">
                                    ${p.name}
                                </h3>
                                <p class="text-[11px] text-teak-500 mt-1.5 line-clamp-2">
                                    ${p.description}
                                </p>
                            </div>
                            <div class="pt-4 mt-3 border-t border-teak-100 flex items-center justify-between">
                                <div>
                                    <span class="block text-[9px] text-teak-400 uppercase">Mulai dari</span>
                                    <span class="text-sm font-bold text-teak-950 font-serif">${PanduApp.formatRupiah(p.price)}</span>
                                </div>
                                <a href="produk.html?id=${p.id}" class="bg-teak-900 hover:bg-amberwood-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition flex items-center gap-1 shadow">
                                    Detail <i data-lucide="chevron-right" class="w-3 h-3"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        if (window.lucide) window.lucide.createIcons();
    }

    if (filterForm) {
        filterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            loadProducts();
        });
    }

    if (categorySelect) categorySelect.addEventListener('change', loadProducts);
    if (sortSelect) sortSelect.addEventListener('change', loadProducts);

    // Initial Load
    await loadProducts();
});
