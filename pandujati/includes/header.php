<?php
// includes/header.php
if (!isset($page_title)) {
    $page_title = 'Pandu Jati Furniture - Furniture Jati Berkualitas Jepara';
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <meta name="description" content="Pandu Jati Furniture - Produsen mebel kayu jati Perhutani berkualitas di Jepara. Melayani pesanan siap kirim & furniture custom seluruh Indonesia.">
    
    <!-- Google Fonts: Plus Jakarta Sans & Playfair Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        teak: {
                            50: '#fdfbf7',
                            100: '#f7f2ea',
                            200: '#ede0cd',
                            300: '#dec6a6',
                            400: '#cca578',
                            500: '#b8854f',
                            600: '#9e6d3e',
                            700: '#7f5433',
                            800: '#68452d',
                            900: '#553927',
                            950: '#2f1e14',
                        },
                        amberwood: {
                            500: '#d97706',
                            600: '#b45309',
                            700: '#92400e'
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Playfair Display"', 'serif'],
                    }
                }
            }
        }
    </script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .wood-texture {
            background-color: #2f1e14;
            background-image: radial-gradient(rgba(218, 165, 32, 0.08) 1px, transparent 0);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="bg-teak-50 text-teak-950 font-sans antialiased flex flex-col min-h-screen">

<!-- Top Bar Information -->
<div class="bg-teak-950 text-teak-200 text-xs py-2 px-4 border-b border-teak-900">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
        <div class="flex items-center gap-4 text-center sm:text-left">
            <span class="flex items-center gap-1.5"><i data-lucide="map-pin" class="w-3.5 h-3.5 text-amberwood-500"></i> Jepara, Jawa Tengah</span>
            <span class="hidden md:inline text-teak-700">•</span>
            <span class="hidden md:flex items-center gap-1.5"><i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-400"></i> Garansi 100% Kayu Jati Legal SVLK</span>
        </div>
        <div class="flex items-center gap-4">
            <a href="https://wa.me/<?= $business['whatsapp'] ?>" target="_blank" class="hover:text-white transition flex items-center gap-1 text-emerald-400 font-medium">
                <i data-lucide="phone" class="w-3.5 h-3.5"></i> <?= $business['phone'] ?>
            </a>
            <span class="text-teak-700">•</span>
            <a href="admin.php" class="hover:text-amberwood-400 transition flex items-center gap-1">
                <i data-lucide="lock" class="w-3 h-3"></i> Portal Admin
            </a>
        </div>
    </div>
</div>

<!-- Main Header / Navbar -->
<header class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-teak-200/60 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <!-- Brand Logo -->
            <a href="index.php" class="flex items-center gap-3 group">
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
            <nav class="hidden lg:flex items-center gap-7 text-sm font-medium text-teak-800">
                <a href="index.php" class="hover:text-amberwood-600 transition <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-amberwood-600 font-semibold' : '' ?>">Beranda</a>
                <a href="katalog.php" class="hover:text-amberwood-600 transition <?= basename($_SERVER['PHP_SELF']) == 'katalog.php' ? 'text-amberwood-600 font-semibold' : '' ?>">Katalog Produk</a>
                <a href="custom.php" class="hover:text-amberwood-600 transition <?= basename($_SERVER['PHP_SELF']) == 'custom.php' ? 'text-amberwood-600 font-semibold' : '' ?>">Furniture Custom</a>
                <a href="tentang-kami.php" class="hover:text-amberwood-600 transition <?= basename($_SERVER['PHP_SELF']) == 'tentang-kami.php' ? 'text-amberwood-600 font-semibold' : '' ?>">Tentang Kami</a>
                <a href="cara-pemesanan.php" class="hover:text-amberwood-600 transition <?= basename($_SERVER['PHP_SELF']) == 'cara-pemesanan.php' ? 'text-amberwood-600 font-semibold' : '' ?>">Cara Pesan</a>
                <a href="cek-pesanan.php" class="hover:text-amberwood-600 transition flex items-center gap-1.5 <?= basename($_SERVER['PHP_SELF']) == 'cek-pesanan.php' ? 'text-amberwood-600 font-semibold' : '' ?>">
                    <i data-lucide="search" class="w-4 h-4 text-amberwood-600"></i> Cek Pesanan
                </a>
                <a href="kontak.php" class="hover:text-amberwood-600 transition <?= basename($_SERVER['PHP_SELF']) == 'kontak.php' ? 'text-amberwood-600 font-semibold' : '' ?>">Kontak</a>
            </nav>

            <!-- CTA Button -->
            <div class="hidden sm:flex items-center gap-3">
                <a href="katalog.php" class="bg-teak-900 hover:bg-teak-800 text-amberwood-400 border border-amberwood-600/40 px-5 py-2.5 rounded-full text-xs font-semibold tracking-wide transition shadow-sm hover:shadow flex items-center gap-2">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i> Pesan Sekarang
                </a>
            </div>

            <!-- Mobile Hamburger Button -->
            <button id="mobile-menu-btn" class="lg:hidden p-2 text-teak-800 hover:text-amberwood-600 rounded-lg hover:bg-teak-100 transition" aria-label="Buka Menu">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Drawer Menu -->
    <div id="mobile-menu" class="hidden lg:hidden bg-white border-b border-teak-200 px-6 py-6 shadow-xl space-y-4">
        <a href="index.php" class="block text-base font-semibold text-teak-900 py-2 border-b border-teak-100">Beranda</a>
        <a href="katalog.php" class="block text-base font-medium text-teak-800 py-2 border-b border-teak-100">Katalog Produk</a>
        <a href="custom.php" class="block text-base font-medium text-teak-800 py-2 border-b border-teak-100">Furniture Custom</a>
        <a href="tentang-kami.php" class="block text-base font-medium text-teak-800 py-2 border-b border-teak-100">Tentang Kami</a>
        <a href="cara-pemesanan.php" class="block text-base font-medium text-teak-800 py-2 border-b border-teak-100">Cara Pemesanan</a>
        <a href="cek-pesanan.php" class="block text-base font-medium text-amberwood-700 py-2 border-b border-teak-100 flex items-center gap-2 font-semibold">
            <i data-lucide="search" class="w-4 h-4"></i> Cek Status Pesanan
        </a>
        <a href="kontak.php" class="block text-base font-medium text-teak-800 py-2 border-b border-teak-100">Kontak Workshop</a>
        
        <div class="pt-2">
            <a href="https://wa.me/<?= $business['whatsapp'] ?>" target="_blank" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-semibold flex items-center justify-center gap-2 text-sm shadow">
                <i data-lucide="message-circle" class="w-4 h-4"></i> Konsultasi WhatsApp
            </a>
        </div>
    </div>
</header>
<main class="flex-grow">
