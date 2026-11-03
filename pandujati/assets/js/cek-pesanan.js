/**
 * ============================================================================
 * JAVASCRIPT: CEK & LACAK STATUS PESANAN (cek-pesanan.html)
 * ============================================================================
 */

document.addEventListener('DOMContentLoaded', async () => {
    PanduApp.init('cek-pesanan');

    const searchForm = document.getElementById('tracking-search-form');
    const orderInput = document.getElementById('order-input');
    const resultContainer = document.getElementById('tracking-result-container');

    const initialId = PanduApp.getQueryParam('id');
    if (orderInput && initialId) {
        orderInput.value = initialId;
        await trackOrder(initialId);
    }

    if (searchForm) {
        searchForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const val = orderInput.value.trim();
            if (val) {
                // Update URL tanpa reload
                const newUrl = `${window.location.pathname}?id=${encodeURIComponent(val)}`;
                window.history.pushState({ path: newUrl }, '', newUrl);
                await trackOrder(val);
            }
        });
    }

    async function trackOrder(orderNumber) {
        if (!resultContainer) return;

        resultContainer.innerHTML = `
            <div class="bg-white rounded-3xl border border-teak-200 p-12 text-center text-teak-600">
                <i data-lucide="loader-2" class="w-8 h-8 animate-spin mx-auto mb-2 text-amberwood-600"></i>
                <p class="text-xs">Mencari riwayat pesanan ${orderNumber}...</p>
            </div>
        `;
        if (window.lucide) window.lucide.createIcons();

        const res = await PanduApi.trackOrder(orderNumber);

        if (!res.success || !res.data) {
            resultContainer.innerHTML = `
                <div class="bg-white rounded-3xl border border-rose-200 p-8 sm:p-12 text-center space-y-4 max-w-lg mx-auto shadow-sm">
                    <div class="w-16 h-16 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center mx-auto">
                        <i data-lucide="alert-triangle" class="w-8 h-8"></i>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-teak-950">Nomor Pesanan Tidak Ditemukan</h3>
                    <p class="text-xs text-teak-600">
                        Nomor pesanan "<strong>${orderNumber}</strong>" tidak terdaftar di sistem kami. Silakan periksa kembali huruf besar/kecil atau hubungi customer service kami jika Anda butuh bantuan.
                    </p>
                    <div class="pt-2">
                        <a href="https://wa.me/${PanduApp.config.whatsapp}?text=Halo%20Pandu%20Jati,%20saya%20mau%20menanyakan%20status%20pesanan%20saya" target="_blank" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-5 py-2.5 rounded-full text-xs font-semibold shadow hover:bg-emerald-700 transition">
                            <i data-lucide="message-circle" class="w-4 h-4"></i> Tanya CS di WhatsApp
                        </a>
                    </div>
                </div>
            `;
            if (window.lucide) window.lucide.createIcons();
            return;
        }

        const { order, items, stages, current_stage_index, whatsapp_cs_url } = res.data;
        const stBadge = PanduApp.getStatusBadge(order.order_status);
        const payBadge = PanduApp.getPaymentBadge(order.payment_status);

        // Render 8-Tahap Visual Timeline
        const stagesHtml = stages.map((stg, idx) => {
            const isPassed = (current_stage_index >= idx);
            const isCurrent = (current_stage_index === idx);

            const dotClass = isCurrent
                ? 'bg-amberwood-600 text-white ring-4 ring-amberwood-100 ring-offset-1 scale-110'
                : (isPassed ? 'bg-emerald-600 text-white' : 'bg-teak-200 text-teak-500');

            const contentClass = isCurrent
                ? 'border-amberwood-400 bg-amberwood-50/30'
                : 'border-teak-200/80';

            const titleColor = isPassed ? 'text-teak-950' : 'text-teak-400';

            const iconOrNumber = (isPassed && !isCurrent)
                ? `<i data-lucide="check" class="w-3.5 h-3.5"></i>`
                : `${idx + 1}`;

            const currentTag = isCurrent
                ? `<span class="text-[10px] font-bold text-amberwood-700 bg-amberwood-100 px-2 py-0.5 rounded-full inline-block mt-1 sm:mt-0">Tahap Saat Ini</span>`
                : '';

            return `
                <div class="relative flex items-start gap-4">
                    <div class="absolute -left-6 sm:-left-8 top-1 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold transition-all ${dotClass}">
                        ${iconOrNumber}
                    </div>
                    <div class="bg-teak-50/70 p-4 rounded-2xl border ${contentClass} w-full transition">
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center">
                            <h4 class="font-serif font-bold text-xs sm:text-sm ${titleColor}">
                                ${stg.title}
                            </h4>
                            ${currentTag}
                        </div>
                        <p class="text-[11px] text-teak-600 mt-1">${stg.description}</p>
                    </div>
                </div>
            `;
        }).join('');

        // Info Ekspedisi / Resi Truk & Catatan Pengrajin
        let expeditionHtml = '';
        if (order.tracking_number || order.admin_notes) {
            expeditionHtml = `
                <div class="bg-amber-50/60 border border-amber-200 rounded-2xl p-5 space-y-2 text-xs">
                    <h4 class="font-serif font-bold text-amber-900 flex items-center gap-1.5">
                        <i data-lucide="truck" class="w-4 h-4 text-amber-700"></i> Informasi Ekspedisi &amp; Catatan Workshop
                    </h4>
                    ${order.tracking_number ? `
                        <p class="text-teak-900">
                            <strong>Nomor Resi / Surat Jalan:</strong> 
                            <span class="font-mono font-bold text-amber-900 bg-white px-2 py-0.5 rounded border border-amber-300">${order.tracking_number}</span>
                        </p>
                    ` : ''}
                    ${order.admin_notes ? `
                        <p class="text-teak-800">
                            <strong>Catatan Pengrajin:</strong> ${order.admin_notes}
                        </p>
                    ` : ''}
                </div>
            `;
        }

        // Rincian Item Barang
        const itemsHtml = items.map(it => `
            <div class="p-4 bg-white flex justify-between items-center">
                <div>
                    <h4 class="font-semibold text-teak-950">${it.product_name}</h4>
                    <span class="text-teak-500 text-[11px]">Finishing: ${it.finishing_choice} • Jumlah: ${it.quantity} unit</span>
                </div>
                <span class="font-bold text-teak-900 font-serif">${PanduApp.formatRupiah(it.subtotal)}</span>
            </div>
        `).join('');

        resultContainer.innerHTML = `
            <div class="bg-white rounded-3xl border border-teak-200/80 p-6 sm:p-8 shadow-sm space-y-8">
                <!-- Header Info -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-teak-100 pb-6">
                    <div>
                        <span class="text-[10px] text-teak-500 uppercase tracking-widest font-bold">Nomor Pesanan Resmi</span>
                        <h2 class="font-mono text-2xl font-bold text-teak-950">${order.order_number}</h2>
                        <span class="text-xs text-teak-500">Tanggal Transaksi: ${order.created_at}</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3.5 py-1.5 rounded-full text-xs font-bold border ${stBadge.class}">
                            ${stBadge.label}
                        </span>
                        <span class="px-3.5 py-1.5 rounded-full text-xs font-bold border ${payBadge.class}">
                            ${payBadge.label}
                        </span>
                    </div>
                </div>

                <!-- Timeline 8 Tahap -->
                <div>
                    <h3 class="font-serif font-bold text-base text-teak-950 mb-6 flex items-center gap-2">
                        <i data-lucide="git-commit" class="w-5 h-5 text-amberwood-600"></i>
                        Perkembangan Tahapan Produksi Kayu
                    </h3>
                    <div class="relative pl-6 sm:pl-8 space-y-6 before:absolute before:left-3 before:top-2 before:bottom-2 before:w-0.5 before:bg-teak-200">
                        ${stagesHtml}
                    </div>
                </div>

                <!-- Info Ekspedisi & Catatan -->
                ${expeditionHtml}

                <!-- Rincian Produk -->
                <div>
                    <h3 class="font-serif font-bold text-base text-teak-950 mb-3 flex items-center gap-2">
                        <i data-lucide="shopping-bag" class="w-5 h-5 text-amberwood-600"></i> Rincian Pesanan
                    </h3>
                    <div class="divide-y divide-teak-100 border border-teak-200 rounded-2xl overflow-hidden text-xs">
                        ${itemsHtml}
                        <div class="p-4 bg-teak-50 flex justify-between items-center text-teak-700">
                            <span>Ongkos Kirim Ekspedisi</span>
                            <span>${PanduApp.formatRupiah(order.shipping_cost)}</span>
                        </div>
                        <div class="p-4 bg-teak-100/70 flex justify-between items-center text-sm font-bold text-teak-950">
                            <span>Total Pembayaran</span>
                            <span class="font-serif text-base text-amberwood-700">${PanduApp.formatRupiah(order.total_amount)}</span>
                        </div>
                    </div>
                </div>

                <!-- Alamat Tujuan -->
                <div class="bg-teak-50/50 p-4 rounded-2xl border border-teak-200 text-xs space-y-1">
                    <span class="font-bold text-teak-900">Alamat Pengiriman Pelanggan:</span>
                    <p class="text-teak-700">${order.customer_name} (${order.customer_phone})</p>
                    <p class="text-teak-600">${order.address}, ${order.city}, ${order.province} ${order.postal_code || ''}</p>
                </div>

                <!-- Tombol Konfirmasi WA -->
                <div class="pt-2 text-center">
                    <a href="${whatsapp_cs_url}" target="_blank" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-7 py-3 rounded-full text-xs shadow transition">
                        <i data-lucide="message-circle" class="w-4 h-4"></i> Konfirmasi Pesanan Ini via WhatsApp
                    </a>
                </div>
            </div>
        `;

        if (window.lucide) window.lucide.createIcons();
    }
});
