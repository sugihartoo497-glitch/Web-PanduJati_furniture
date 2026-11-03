<?php
require_once 'config.php';
$page_title = 'Pesan Custom Furniture Kayu Jati - Pandu Jati Furniture';

$success_custom = null;
$error_msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name   = trim($_POST['customer_name'] ?? '');
    $customer_phone  = trim($_POST['customer_phone'] ?? '');
    $customer_email  = trim($_POST['customer_email'] ?? '');
    $furniture_type  = trim($_POST['furniture_type'] ?? '');
    $dimensions      = trim($_POST['dimensions'] ?? '');
    $wood_type       = trim($_POST['wood_type'] ?? 'Kayu Jati Solid TPK Perhutani');
    $finishing       = trim($_POST['finishing'] ?? 'Natural Teak Melamine Doff');
    $budget_range    = trim($_POST['budget_range'] ?? '');
    $description     = trim($_POST['description'] ?? '');
    $reference_image = trim($_POST['reference_image'] ?? '');

    if (empty($customer_name) || empty($customer_phone) || empty($furniture_type) || empty($description)) {
        $error_msg = "Harap lengkapi nama, nomor WhatsApp, jenis furniture, dan deskripsi kebutuhan custom Anda.";
    } else {
        $request_number = generateCustomNumber();

        try {
            $stmt = $pdo->prepare("INSERT INTO custom_requests 
                (request_number, customer_name, customer_phone, customer_email, furniture_type, dimensions, wood_type, finishing, description, reference_image, budget_range, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'PENDING')");
            $stmt->execute([
                $request_number,
                $customer_name,
                $customer_phone,
                $customer_email,
                $furniture_type,
                $dimensions,
                $wood_type,
                $finishing,
                $description,
                $reference_image,
                $budget_range
            ]);

            $success_custom = [
                'request_number' => $request_number,
                'customer_name' => $customer_name,
                'customer_phone' => $customer_phone,
                'furniture_type' => $furniture_type,
                'wood_type' => $wood_type,
                'budget_range' => $budget_range
            ];
        } catch (Exception $e) {
            $error_msg = "Gagal mengirimkan request: " . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<div class="bg-teak-950 text-white py-12 wood-texture">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-3">
        <span class="text-xs font-bold text-amberwood-400 uppercase tracking-widest">Layanan Rancang Bangun Mebel</span>
        <h1 class="font-serif text-3xl sm:text-4xl font-bold">Pesan Furniture Custom Jepara</h1>
        <p class="text-xs sm:text-sm text-teak-300 max-w-xl mx-auto font-light">
            Sesuaikan ukuran mebel dengan luas ruangan rumah, apartemen, cafe, atau kantor Anda. Kami hitungkan estimasi biaya secara transparan.
        </p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <?php if ($success_custom): ?>
    <div class="bg-white rounded-3xl border border-teak-200/80 p-8 sm:p-12 shadow-xl text-center space-y-6">
        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto">
            <i data-lucide="check-circle" class="w-10 h-10"></i>
        </div>

        <div class="space-y-2">
            <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Permintaan Diterima</span>
            <h2 class="font-serif text-2xl sm:text-3xl font-bold text-teak-950">Terima Kasih, <?= htmlspecialchars($success_custom['customer_name']) ?>!</h2>
            <p class="text-xs sm:text-sm text-teak-600 max-w-md mx-auto">
                Permintaan custom furniture Anda telah masuk ke meja pengrajin kami. Kami akan segera menghubungi WhatsApp Anda untuk kalkulasi dan estimasi biaya.
            </p>
        </div>

        <div class="bg-teak-50 border-2 border-dashed border-amberwood-400 p-6 rounded-2xl max-w-md mx-auto space-y-2">
            <span class="text-[10px] text-teak-500 uppercase tracking-widest block font-bold">Nomor Tiket Custom</span>
            <span class="font-mono text-2xl font-bold text-teak-950 tracking-wider block">
                <?= $success_custom['request_number'] ?>
            </span>
            <span class="text-[11px] text-teak-500 block"><?= htmlspecialchars($success_custom['furniture_type']) ?> (<?= htmlspecialchars($success_custom['wood_type']) ?>)</span>
        </div>

        <?php
        $wa_custom_msg = "Halo Pandu Jati Furniture,\nSaya baru saja mengirimkan Request Custom Furniture.\n\n"
                       . "Nomor Tiket: " . $success_custom['request_number'] . "\n"
                       . "Nama: " . $success_custom['customer_name'] . "\n"
                       . "Jenis Mebel: " . $success_custom['furniture_type'] . "\n"
                       . "Bahan Kayu: " . $success_custom['wood_type'] . "\n"
                       . "Budget: " . $success_custom['budget_range'] . "\n\n"
                       . "Mohon estimasi penawaran harganya. Terima kasih.";
        $wa_custom_url = "https://wa.me/" . $business['whatsapp'] . "?text=" . urlencode($wa_custom_msg);
        ?>
        <div class="pt-2 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="<?= $wa_custom_url ?>" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 px-8 rounded-full text-xs sm:text-sm flex items-center justify-center gap-2 shadow-lg transition">
                <i data-lucide="message-circle" class="w-5 h-5"></i> Kirim Rincian Tiket ke WhatsApp CS
            </a>
            <a href="index.php" class="bg-teak-100 hover:bg-teak-200 text-teak-900 font-bold py-3.5 px-8 rounded-full text-xs sm:text-sm flex items-center justify-center gap-2 transition">
                Kembali ke Beranda
            </a>
        </div>
    </div>

    <?php else: ?>

    <?php if ($error_msg): ?>
    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm flex items-center gap-2">
        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 flex-shrink-0"></i>
        <span><?= htmlspecialchars($error_msg) ?></span>
    </div>
    <?php endif; ?>

    <form method="POST" action="custom.php" class="bg-white rounded-3xl border border-teak-200/80 p-6 sm:p-10 shadow-sm space-y-8">
        <!-- Data Pemesan -->
        <div>
            <h3 class="font-serif font-bold text-lg text-teak-950 border-b border-teak-100 pb-3 mb-4 flex items-center gap-2">
                <i data-lucide="user" class="w-5 h-5 text-amberwood-600"></i> 1. Kontak Pemesan
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-teak-800 mb-1.5">Nama Lengkap *</label>
                    <input type="text" name="customer_name" required placeholder="Contoh: Ibu Rina Melati" class="w-full px-4 py-3 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500">
                </div>
                <div>
                    <label class="block font-semibold text-teak-800 mb-1.5">Nomor WhatsApp Aktif *</label>
                    <input type="tel" name="customer_phone" required placeholder="Contoh: 081234567890" class="w-full px-4 py-3 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block font-semibold text-teak-800 mb-1.5">Alamat Email (Opsional)</label>
                    <input type="email" name="customer_email" placeholder="rina@gmail.com" class="w-full px-4 py-3 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500">
                </div>
            </div>
        </div>

        <!-- Spesifikasi Custom -->
        <div>
            <h3 class="font-serif font-bold text-lg text-teak-950 border-b border-teak-100 pb-3 mb-4 flex items-center gap-2">
                <i data-lucide="compass" class="w-5 h-5 text-amberwood-600"></i> 2. Rincian Desain &amp; Kebutuhan Mebel
            </h3>
            <div class="space-y-4 text-xs">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-teak-800 mb-1.5">Jenis Furniture yang Ingin Dibuat *</label>
                        <input type="text" name="furniture_type" required placeholder="Contoh: Meja Rapat 12 Kursi / Kitchen Set L / Dipan Laci" class="w-full px-4 py-3 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-teak-800 mb-1.5">Estimasi Ukuran (Panjang x Lebar x Tinggi)</label>
                        <input type="text" name="dimensions" placeholder="Contoh: P 240 x L 110 x T 75 cm" class="w-full px-4 py-3 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-teak-800 mb-1.5">Pilihan Jenis Kayu</label>
                        <select name="wood_type" class="w-full px-4 py-3 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500 bg-white">
                            <option value="Kayu Jati Solid TPK Perhutani Grade A">Kayu Jati Solid TPK Perhutani Grade A (Sangat Direkomendasikan)</option>
                            <option value="Kayu Jati Rakyat Kering Oven">Kayu Jati Rakyat Kering Oven</option>
                            <option value="Kayu Mahoni Oven Grade Ekspor">Kayu Mahoni Oven (Khusus Finishing Duco Putih/Warna)</option>
                            <option value="Kayu Trembesi / Suar Wood Solid">Kayu Trembesi / Suar Solid (Meja Tebal Utuh)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-teak-800 mb-1.5">Pilihan Warna &amp; Finishing</label>
                        <select name="finishing" class="w-full px-4 py-3 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500 bg-white">
                            <option value="Natural Teak Doff">Natural Teak Doff (Serat Kayu Asli Terlihat)</option>
                            <option value="Walnut Brown Matte">Walnut Brown Matte (Cokelat Hangat)</option>
                            <option value="Salak Brown Gloss">Salak Brown Gloss (Cokelat Tua Mengkilap)</option>
                            <option value="Bleaching Modern Light Teak">Bleaching Modern Light Teak (Krem Natural)</option>
                            <option value="Duco Putih / Hitam">Cat Duco Mobil (Putih / Hitam Mulus)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-teak-800 mb-1.5">Kisaran Anggaran / Budget Anda</label>
                        <select name="budget_range" class="w-full px-4 py-3 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500 bg-white">
                            <option value="Dibawah Rp 5 Juta">Dibawah Rp 5 Juta</option>
                            <option value="Rp 5 Juta - Rp 10 Juta">Rp 5 Juta - Rp 10 Juta</option>
                            <option value="Rp 10 Juta - Rp 25 Juta">Rp 10 Juta - Rp 25 Juta</option>
                            <option value="Diatas Rp 25 Juta">Diatas Rp 25 Juta</option>
                            <option value="Fleksibel sesuai kualitas kayu">Fleksibel sesuai kualitas kayu terbaik</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-teak-800 mb-1.5">Link Gambar Referensi Desain (Pinterest / Google Drive / Instagram)</label>
                        <input type="url" name="reference_image" placeholder="https://pinterest.com/pin/..." class="w-full px-4 py-3 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-teak-800 mb-1.5">Deskripsi Lengkap Kebutuhan Mebel *</label>
                    <textarea name="description" required rows="4" placeholder="Jelaskan kebutuhan Anda secara detail: jumlah laci, model kaki meja, ketebalan papan kayu, dan detail khusus lainnya..." class="w-full px-4 py-3 rounded-2xl border border-teak-200 focus:outline-none focus:ring-2 focus:ring-amberwood-500"></textarea>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-teak-100">
            <button type="submit" class="w-full bg-teak-900 hover:bg-amberwood-600 text-white font-bold py-4 rounded-2xl text-center transition flex items-center justify-center gap-2 shadow-lg text-sm sm:text-base">
                <i data-lucide="send" class="w-5 h-5 text-amberwood-400"></i> Kirim Request Custom &amp; Buat Tiket Konsultasi
            </button>
        </div>
    </form>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
