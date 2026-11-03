/**
 * ============================================================================
 * JAVASCRIPT: CHECKOUT & PEMESANAN ONLINE (pesanan.html)
 * ============================================================================
 */

document.addEventListener('DOMContentLoaded', async () => {
    PanduApp.init('pesanan');

    const form = document.getElementById('order-form');
    const productSelect = document.getElementById('product-select');
    const quantityInput = document.getElementById('quantity-input');
    const shippingRadios = document.querySelectorAll('input[name="shipping_option"]');
    const subtotalDisplay = document.getElementById('calc-subtotal');
    const shippingDisplay = document.getElementById('calc-shipping');
    const grandTotalDisplay = document.getElementById('calc-total');
    const orderFormContainer = document.getElementById('order-form-container');
    const orderSuccessContainer = document.getElementById('order-success-container');
    const errorBox = document.getElementById('order-error-box');

    let allProducts = [];
    const shippingCosts = [250000, 450000, 0]; // Index 0: Truk Jepara, 1: Kargo Peti Kayu, 2: Ambil Sendiri
    const prefillId = parseInt(PanduApp.getQueryParam('produk_id'), 10) || 0;

    // 1. Ambil daftar produk dari backend untuk dropdown
    try {
        const res = await PanduApi.getProducts();
        if (res.success && res.data) {
            allProducts = res.data.products;
            if (productSelect) {
                allProducts.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.id;
                    opt.setAttribute('data-price', p.price);
                    opt.textContent = `${p.name} - ${PanduApp.formatRupiah(p.price)} (Stok: ${p.stock})`;
                    if (p.id === prefillId) opt.selected = true;
                    productSelect.appendChild(opt);
                });
            }
        }
    } catch (e) {
        console.error('Error fetching products for checkout:', e);
    }

    // 2. Kalkulasi Total Real-time
    function calculateTotals() {
        const selectedId = parseInt(productSelect?.value, 10);
        const selectedProduct = allProducts.find(p => p.id === selectedId);
        const qty = parseInt(quantityInput?.value, 10) || 1;

        const price = selectedProduct ? parseFloat(selectedProduct.price) : 0;
        const subtotal = price * qty;

        let shippingCost = 250000;
        shippingRadios.forEach((r, idx) => {
            if (r.checked) shippingCost = shippingCosts[idx] ?? 250000;
        });

        const grandTotal = subtotal + shippingCost;

        if (subtotalDisplay) subtotalDisplay.innerText = PanduApp.formatRupiah(subtotal);
        if (shippingDisplay) shippingDisplay.innerText = PanduApp.formatRupiah(shippingCost);
        if (grandTotalDisplay) grandTotalDisplay.innerText = PanduApp.formatRupiah(grandTotal);
    }

    if (productSelect) productSelect.addEventListener('change', calculateTotals);
    if (quantityInput) quantityInput.addEventListener('input', calculateTotals);
    shippingRadios.forEach(r => r.addEventListener('change', calculateTotals));

    calculateTotals();

    // 3. Form Submit
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (errorBox) errorBox.classList.add('hidden');

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i> Memproses Pesanan...`;
            if (window.lucide) window.lucide.createIcons();

            let selectedShippingIndex = 0;
            shippingRadios.forEach((r, idx) => {
                if (r.checked) selectedShippingIndex = idx;
            });

            const payload = {
                customer_name: form.customer_name.value.trim(),
                customer_phone: form.customer_phone.value.trim(),
                customer_email: form.customer_email.value.trim(),
                address: form.address.value.trim(),
                city: form.city.value.trim(),
                province: form.province.value.trim(),
                postal_code: form.postal_code.value.trim(),
                product_id: parseInt(form.product_id.value, 10),
                quantity: parseInt(form.quantity.value, 10) || 1,
                finishing_choice: form.finishing.value,
                shipping_option_index: selectedShippingIndex,
                payment_term: form.payment_term.value,
                customer_notes: form.customer_notes.value.trim()
            };

            const res = await PanduApi.createOrder(payload);

            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
            if (window.lucide) window.lucide.createIcons();

            if (!res.success || !res.data) {
                if (errorBox) {
                    errorBox.innerText = res.message || 'Gagal memproses pesanan.';
                    errorBox.classList.remove('hidden');
                }
                return;
            }

            const order = res.data;

            // Tampilkan Layar Sukses Pesanan
            if (orderFormContainer && orderSuccessContainer) {
                orderFormContainer.classList.add('hidden');
                orderSuccessContainer.classList.remove('hidden');

                orderSuccessContainer.innerHTML = `
                    <div class="bg-white rounded-3xl border border-teak-200/80 p-8 sm:p-12 shadow-xl space-y-8 text-center max-w-3xl mx-auto">
                        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto shadow-inner">
                            <i data-lucide="check-circle" class="w-10 h-10"></i>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Pesanan Berhasil Dibuat</span>
                            <h2 class="font-serif text-2xl sm:text-3xl font-bold text-teak-950">Terima Kasih, ${order.customer_name}!</h2>
                            <p class="text-xs sm:text-sm text-teak-600 max-w-md mx-auto">
                                Pesanan Anda telah tersimpan di sistem workshop kami. Silakan simpan nomor pesanan berikut untuk melacak status pengerjaan kayu dan pengiriman:
                            </p>
                        </div>

                        <!-- Box Nomor Pesanan Unik -->
                        <div class="bg-teak-50 border-2 border-dashed border-amberwood-400 p-6 rounded-2xl max-w-md mx-auto space-y-2">
                            <span class="text-[10px] text-teak-500 uppercase tracking-widest block font-bold">Nomor Pesanan Unik Anda</span>
                            <span class="font-mono text-2xl sm:text-3xl font-bold text-teak-950 tracking-wider block selection:bg-amberwood-200">
                                ${order.order_number}
                            </span>
                            <span class="text-[11px] text-teak-500 block">Simpan nomor ini untuk pengecekan di menu "Cek Pesanan"</span>
                        </div>

                        <!-- Ringkasan Singkat -->
                        <div class="bg-teak-50/50 rounded-2xl p-6 text-left max-w-lg mx-auto text-xs space-y-2 border border-teak-200">
                            <div class="flex justify-between border-b border-teak-100 pb-2">
                                <span class="text-teak-500">Produk Mebel</span>
                                <span class="font-semibold text-teak-900">${order.product_name} (${order.quantity}x)</span>
                            </div>
                            <div class="flex justify-between border-b border-teak-100 pb-2">
                                <span class="text-teak-500">Total Pembayaran</span>
                                <span class="font-bold text-base text-amberwood-700 font-serif">${PanduApp.formatRupiah(order.total_amount)}</span>
                            </div>
                            <div class="flex justify-between border-b border-teak-100 pb-2">
                                <span class="text-teak-500">Metode Bayar</span>
                                <span class="font-semibold text-teak-900">${order.payment_method}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-teak-500">Ekspedisi</span>
                                <span class="font-semibold text-teak-900">${order.shipping_method}</span>
                            </div>
                        </div>

                        <!-- Rekening Bank -->
                        <div class="max-w-lg mx-auto p-4 bg-amber-50 border border-amber-200 rounded-2xl text-left text-xs space-y-3">
                            <h4 class="font-bold text-amber-900 flex items-center gap-1.5">
                                <i data-lucide="credit-card" class="w-4 h-4 text-amber-700"></i> Rekening Pembayaran Resmi:
                            </h4>
                            <div class="space-y-1.5">
                                <div class="flex justify-between items-center bg-white p-2.5 rounded-xl border border-amber-200">
                                    <span class="font-bold text-teak-900">Bank BCA</span>
                                    <span class="font-mono font-bold text-amber-900">1234-567-890</span>
                                    <span class="text-[10px] text-teak-500">(a/n Pandu Jati Furniture)</span>
                                </div>
                                <div class="flex justify-between items-center bg-white p-2.5 rounded-xl border border-amber-200">
                                    <span class="font-bold text-teak-900">Bank Mandiri</span>
                                    <span class="font-mono font-bold text-amber-900">138-00-1234567-8</span>
                                    <span class="text-[10px] text-teak-500">(a/n Pandu Jati Furniture)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-4 flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="${order.whatsapp_url}" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 px-8 rounded-full text-xs sm:text-sm flex items-center justify-center gap-2 shadow-lg transition">
                                <i data-lucide="message-circle" class="w-5 h-5"></i> Kirim Detail Pesanan ke WhatsApp
                            </a>
                            <a href="cek-pesanan.html?id=${order.order_number}" class="bg-teak-900 hover:bg-teak-800 text-white font-bold py-3.5 px-8 rounded-full text-xs sm:text-sm flex items-center justify-center gap-2 shadow transition">
                                <i data-lucide="search" class="w-4 h-4 text-amberwood-400"></i> Lacak Status Pesanan
                            </a>
                        </div>
                    </div>
                `;

                if (window.lucide) window.lucide.createIcons();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    }
});
