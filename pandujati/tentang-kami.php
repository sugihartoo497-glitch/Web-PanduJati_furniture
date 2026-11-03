<?php
require_once 'config.php';
$page_title = 'Tentang Kami - Pandu Jati Furniture Jepara';
include 'includes/header.php';
?>

<div class="bg-teak-950 text-white py-14 wood-texture">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-3">
        <span class="text-xs font-bold text-amberwood-400 uppercase tracking-widest">Keahlian Dari Sentra Kayu Jati</span>
        <h1 class="font-serif text-3xl sm:text-5xl font-bold">Tentang Pandu Jati Furniture</h1>
        <p class="text-xs sm:text-sm text-teak-300 max-w-xl mx-auto font-light">
            Menjaga keaslian tradisi pertukangan kayu jati Jepara dengan perpaduan desain modern, fungsional, dan daya tahan puluhan tahun.
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 space-y-16">
    <!-- Profil & Visi -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div class="space-y-5 text-xs sm:text-sm text-teak-700 leading-relaxed">
            <span class="text-xs font-bold text-amberwood-600 uppercase tracking-wider block">Workshop Spesialis Jati</span>
            <h2 class="font-serif text-2xl sm:text-3xl font-bold text-teak-950">
                Lahir dari Tradisi Mebel Ukir &amp; Minimalis Jepara
            </h2>
            <p>
                <strong>Pandu Jati Furniture</strong> adalah produsen dan bengkel kayu yang berakar di jantung industri mebel Kabupaten Jepara, Jawa Tengah. Kami mengkhususkan diri pada pembuatan berbagai furniture berbahan <em>Kayu Jati Solid (Tectona grandis)</em> bersertifikat legalitas kayu (SVLK).
            </p>
            <p>
                Bagi kami, furniture kayu bukan sekadar perabotan pengisi ruangan, melainkan karya seni yang akan menemani kehangatan keluarga hingga generasi berikutnya. Setiap helai serat kayu jati dipilih secara cermat dan diolah melalui proses pengeringan oven (Kiln Dried) terkontrol untuk mencegah susut atau retak di kemudian hari.
            </p>
        </div>
        <div class="rounded-3xl overflow-hidden border border-teak-200 shadow-xl bg-teak-100">
            <img src="https://images.unsplash.com/photo-1540518614846-7ede433c4570?auto=format&fit=crop&w=800&q=80" alt="Pengrajin Pandu Jati" class="w-full h-96 object-cover">
        </div>
    </div>

    <!-- 4 Pilar Kualitas -->
    <div class="bg-white rounded-3xl border border-teak-200/80 p-8 sm:p-12 shadow-sm">
        <div class="text-center max-w-xl mx-auto mb-10">
            <span class="text-xs font-bold text-amberwood-600 uppercase tracking-widest">Kualitas Tanpa Kompromi</span>
            <h3 class="font-serif text-2xl sm:text-3xl font-bold text-teak-950 mt-1">4 Pilar Konstruksi Pandu Jati</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 text-center">
            <div class="p-5 rounded-2xl bg-teak-50 border border-teak-200/60 space-y-2">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amberwood-700 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="trees" class="w-6 h-6"></i>
                </div>
                <h4 class="font-bold text-sm text-teak-950">Kayu Jati Perhutani</h4>
                <p class="text-xs text-teak-600">Bahan kayu jati legal TPK berumur matang dengan kandungan minyak alami tinggi yang tahan rayap.</p>
            </div>
            <div class="p-5 rounded-2xl bg-teak-50 border border-teak-200/60 space-y-2">
                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="layers" class="w-6 h-6"></i>
                </div>
                <h4 class="font-bold text-sm text-teak-950">Sambungan Purus-Pasak</h4>
                <p class="text-xs text-teak-600">Sistem konstruksi mortise and tenon tradisional tanpa paku berlebih yang menjamin kekokohan seumur hidup.</p>
            </div>
            <div class="p-5 rounded-2xl bg-teak-50 border border-teak-200/60 space-y-2">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="sparkle" class="w-6 h-6"></i>
                </div>
                <h4 class="font-bold text-sm text-teak-950">Finishing Melamine Doff</h4>
                <p class="text-xs text-teak-600">Pelapisan akhir halus dan tahan gores yang tetap menonjolkan keindahan serat alami kayu jati.</p>
            </div>
            <div class="p-5 rounded-2xl bg-teak-50 border border-teak-200/60 space-y-2">
                <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="package-check" class="w-6 h-6"></i>
                </div>
                <h4 class="font-bold text-sm text-teak-950">Peti Kayu Ekspedisi</h4>
                <p class="text-xs text-teak-600">Proteksi 3 lapis (foam sheet, karton single face, dan rangka kayu solid) selama perjalanan kargo.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
