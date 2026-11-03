/**
 * ============================================================================
 * JAVASCRIPT: PERMINTAAN CUSTOM FURNITURE (custom.html)
 * ============================================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    PanduApp.init('custom');

    const form = document.getElementById('custom-form');
    const formContainer = document.getElementById('custom-form-container');
    const successContainer = document.getElementById('custom-success-container');
    const errorBox = document.getElementById('custom-error-box');

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (errorBox) errorBox.classList.add('hidden');

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i> Mengirim Request...`;
            if (window.lucide) window.lucide.createIcons();

            const payload = {
                customer_name: form.customer_name.value.trim(),
                customer_phone: form.customer_phone.value.trim(),
                customer_email: form.customer_email.value.trim(),
                furniture_type: form.furniture_type.value.trim(),
                dimensions: form.dimensions.value.trim(),
                wood_type: form.wood_type.value,
                finishing: form.finishing.value,
                budget_range: form.budget_range.value,
                description: form.description.value.trim(),
                reference_image: form.reference_image.value.trim()
            };

            const res = await PanduApi.submitCustomRequest(payload);

            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            if (window.lucide) window.lucide.createIcons();

            if (!res.success || !res.data) {
                if (errorBox) {
                    errorBox.innerText = res.message || 'Gagal mengirimkan request custom.';
                    errorBox.classList.remove('hidden');
                }
                return;
            }

            const data = res.data;

            if (formContainer && successContainer) {
                formContainer.classList.add('hidden');
                successContainer.classList.remove('hidden');

                successContainer.innerHTML = `
                    <div class="bg-white rounded-3xl border border-teak-200/80 p-8 sm:p-12 shadow-xl text-center space-y-6 max-w-2xl mx-auto">
                        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto">
                            <i data-lucide="check-circle" class="w-10 h-10"></i>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Permintaan Diterima</span>
                            <h2 class="font-serif text-2xl sm:text-3xl font-bold text-teak-950">Terima Kasih, ${data.customer_name}!</h2>
                            <p class="text-xs sm:text-sm text-teak-600 max-w-md mx-auto">
                                Permintaan custom mebel Anda telah masuk ke meja pengrajin Pandu Jati. Kami akan segera menghubungi WhatsApp Anda untuk kalkulasi dan estimasi biaya yang transparan.
                            </p>
                        </div>

                        <div class="bg-teak-50 border-2 border-dashed border-amberwood-400 p-6 rounded-2xl max-w-md mx-auto space-y-2">
                            <span class="text-[10px] text-teak-500 uppercase tracking-widest block font-bold">Nomor Tiket Custom Anda</span>
                            <span class="font-mono text-2xl font-bold text-teak-950 tracking-wider block">
                                ${data.request_number}
                            </span>
                            <span class="text-[11px] text-teak-500 block">${data.furniture_type} (${data.wood_type})</span>
                        </div>

                        <div class="pt-2 flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="${data.whatsapp_cs_url}" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 px-8 rounded-full text-xs sm:text-sm flex items-center justify-center gap-2 shadow-lg transition">
                                <i data-lucide="message-circle" class="w-5 h-5"></i> Kirim Rincian Tiket ke WhatsApp CS
                            </a>
                            <a href="index.html" class="bg-teak-100 hover:bg-teak-200 text-teak-900 font-bold py-3.5 px-8 rounded-full text-xs sm:text-sm flex items-center justify-center gap-2 transition">
                                Kembali ke Beranda
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
