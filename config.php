<?php
// C:\laragon\www\pandujati\config.php
// Konfigurasi Database & Informasi Bisnis Pandu Jati Furniture

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Credentials (Laragon default)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pandu_jati_db');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}

// Global Business Information
$business = [
    'brand_name' => 'Pandu Jati Furniture',
    'legal_name' => 'CV. Pandu Jati Furniture Jepara',
    'tagline' => 'Furniture Jati Berkualitas untuk Rumah Impian Anda',
    'phone' => '+62 851-9123-5955',
    'whatsapp' => '6285191235955',
    'email' => 'info@pandujatifurniture.com',
    'address' => 'Jl. Raya Tahunan - Batealit KM 3.5, Sentra Industri Mebel Kayu Jati, Jepara, Jawa Tengah 59427',
    'hours' => 'Senin - Sabtu: 08:00 - 17:00 WIB (Konsultasi Online 24 Jam)',
    'instagram' => '@pandujati.furniture',
    'tiktok' => '@pandujatifurniture',
    'bank_accounts' => [
        ['bank' => 'BCA', 'number' => '1234-567-890', 'holder' => 'Pandu Jati Furniture'],
        ['bank' => 'Mandiri', 'number' => '138-00-1234567-8', 'holder' => 'Pandu Jati Furniture'],
        ['bank' => 'BRI', 'number' => '0023-01-001234-50-8', 'holder' => 'Pandu Jati Furniture']
    ],
    'shipping_options' => [
        ['id' => 'truk-jepara', 'name' => 'Truk Ekspedisi Khusus Mebel Jepara (Jawa - Bali - Sumatera)', 'cost' => 250000, 'time' => '2 - 5 Hari Kerja'],
        ['id' => 'kargo-crate', 'name' => 'Kargo Darat/Laut + Peti Kayu Solid (Luar Pulau)', 'cost' => 450000, 'time' => '5 - 10 Hari Kerja'],
        ['id' => 'pickup', 'name' => 'Ambil Sendiri di Workshop Jepara', 'cost' => 0, 'time' => 'Sesuai kesepakatan']
    ]
];

// Helper Functions
function formatRupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function generateOrderNumber() {
    $datePart = date('Ymd');
    $random = mt_rand(1000, 9999);
    return 'PJF-' . $datePart . '-' . $random;
}

function generateCustomNumber() {
    $datePart = date('Ymd');
    $random = mt_rand(1000, 9999);
    return 'PJF-CST-' . $datePart . '-' . $random;
}

function getStatusBadge($status) {
    $badges = [
        'PENDING' => ['label' => 'Pesanan Dibuat', 'class' => 'bg-amber-100 text-amber-800 border-amber-300'],
        'CONFIRMED' => ['label' => 'Pesanan Dikonfirmasi', 'class' => 'bg-blue-100 text-blue-800 border-blue-300'],
        'PROCESSING' => ['label' => 'Sedang Diproses', 'class' => 'bg-indigo-100 text-indigo-800 border-indigo-300'],
        'PRODUCTION' => ['label' => 'Sedang Dibuat (Produksi)', 'class' => 'bg-orange-100 text-orange-800 border-orange-300'],
        'QUALITY_CHECK' => ['label' => 'Quality Check', 'class' => 'bg-purple-100 text-purple-800 border-purple-300'],
        'READY_TO_SHIP' => ['label' => 'Siap Dikirim', 'class' => 'bg-cyan-100 text-cyan-800 border-cyan-300'],
        'SHIPPED' => ['label' => 'Dalam Pengiriman', 'class' => 'bg-sky-100 text-sky-800 border-sky-300'],
        'COMPLETED' => ['label' => 'Pesanan Selesai', 'class' => 'bg-emerald-100 text-emerald-800 border-emerald-300'],
        'CANCELLED' => ['label' => 'Dibatalkan', 'class' => 'bg-rose-100 text-rose-800 border-rose-300'],
    ];
    return $badges[$status] ?? ['label' => $status, 'class' => 'bg-gray-100 text-gray-800 border-gray-300'];
}

function getPaymentBadge($status) {
    $badges = [
        'UNPAID' => ['label' => 'Belum Dibayar', 'class' => 'bg-rose-100 text-rose-800 border-rose-300'],
        'DP' => ['label' => 'DP 50% Masuk', 'class' => 'bg-blue-100 text-blue-800 border-blue-300'],
        'PAID' => ['label' => 'Lunas', 'class' => 'bg-emerald-100 text-emerald-800 border-emerald-300'],
        'REFUNDED' => ['label' => 'Refund', 'class' => 'bg-gray-100 text-gray-800 border-gray-300'],
    ];
    return $badges[$status] ?? ['label' => $status, 'class' => 'bg-gray-100 text-gray-800 border-gray-300'];
}
