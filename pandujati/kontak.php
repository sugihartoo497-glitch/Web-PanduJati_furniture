<?php
require_once 'config.php';
$page_title = 'Kontak Workshop - Pandu Jati Furniture Jepara';

$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sent = true;
}

include 'includes/header.php';
?>

<div class="bg-teak-950 text-white py-14 wood-texture">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-3">
        <span class="text-xs font-bold text-amberwood-400 uppercase tracking-widest">Sentra Industri Mebel</span>
        <h1 class="font-serif text-3xl sm:text-5xl font-bold">Kontak &amp; Lokasi Workshop</h1>
        <p class="text-xs sm:text-sm text-teak-300 max-w-xl mx-auto font-light">
            Silakan berkunjung langsung ke workshop dan galeri kami di Jepara, atau hubungi saluran konsultasi online kami.
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Informasi Workshop (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white rounded-3xl border border-teak-200/80 p-6 sm:p-8 shadow-sm space-y-6">
                <h3 class="font-serif font-bold text-lg text-teak-950 border-b border-teak-100 pb-3">Informasi Workshop</h3>

                <div class="space-y-4 text-xs">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amberwood-700 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="map-pin" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="font-bold text-teak-900 block text-sm">Alamat Workshop</span>
                            <span class="text-teak-600 leading-relaxed"><?= htmlspecialchars($business['address']) ?></span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="phone" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="font-bold text-teak-900 block text-sm">WhatsApp / Telepon</span>
                            <span class="text-emerald-700 font-semibold"><?= htmlspecialchars($business['phone']) ?></span>
                            <span class="block text-teak-500 text-[11px]">Respon cepat 08:00 - 21:00 WIB</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="clock" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="font-bold text-teak-900 block text-sm">Jam Operasional</span>
                            <span class="text-teak-600"><?= htmlspecialchars($business['hours']) ?></span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="mail" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="font-bold text-teak-900 block text-sm">Email Resmi</span>
                            <span class="text-teak-600"><?= htmlspecialchars($business['email']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <a href="https://wa.me/<?= $business['whatsapp'] ?>?text=Halo%20Pandu%20Jati,%20saya%20mau%20konsultasi%20mebel" target="_blank" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-2xl flex items-center justify-center gap-2 text-xs shadow transition">
                        <i data-lucide="message-circle" class="w-4 h-4"></i> Chat Langsung via WhatsApp
                    </a>
                </div>
            </div>
        </div>

        <!-- Google Maps Embed & Pesan (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Peta Google Maps Embed -->
            <div class="bg-white rounded-3xl border border-teak-200/80 p-3 shadow-sm overflow-hidden">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126756.24869814468!2d110.63914484999999!3d-6.589886299999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e711f1816bb1c31%3A0xb3a82ef45b736b0!2sJepara%2C%20Jepara%20Regency%2C%20Central%20Java!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid" width="100%" height="340" style="border:0; border-radius: 1.25rem;" allowfullscreen="" loading="lazy"></iframe>
            </div>

            <!-- Kirim Pesan Cepat -->
            <div class="bg-white rounded-3xl border border-teak-200/80 p-6 sm:p-8 shadow-sm">
                <h3 class="font-serif font-bold text-lg text-teak-950 mb-4">Tinggalkan Pesan</h3>
                <?php if ($sent): ?>
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2 mb-4">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
                    <span>Pesan Anda berhasil terkirim! Tim kami akan segera menghubungi Anda kembali.</span>
                </div>
                <?php endif; ?>
                <form method="POST" action="kontak.php" class="space-y-4 text-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-teak-800 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" required placeholder="Bpk/Ibu..." class="w-full px-4 py-2.5 rounded-xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-teak-800 mb-1">No. WhatsApp</label>
                            <input type="tel" name="phone" required placeholder="08..." class="w-full px-4 py-2.5 rounded-xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500">
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold text-teak-800 mb-1">Pesan / Pertanyaan</label>
                        <textarea name="message" required rows="3" placeholder="Tuliskan pertanyaan Anda..." class="w-full px-4 py-2.5 rounded-xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500"></textarea>
                    </div>
                    <button type="submit" class="bg-teak-900 hover:bg-amberwood-600 text-white font-semibold px-6 py-2.5 rounded-xl text-xs transition flex items-center gap-1.5 shadow">
                        <i data-lucide="send" class="w-3.5 h-3.5"></i> Kirim Pesan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
