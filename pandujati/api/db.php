<?php
/**
 * ============================================================================
 * BACKEND PANDU JATI FURNITURE - KONEKSI DATABASE & HELPER API
 * ============================================================================
 * File ini menangani koneksi ke MySQL menggunakan PDO, pengaturan CORS header,
 * dan fungsi pembantu (helper) untuk mengirim respon JSON ke frontend JS/TS.
 */

// Mulai session untuk otentikasi admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Header agar respon selalu dalam format JSON & bisa diakses frontend
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight request dari browser
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Konfigurasi Database (Default Laragon)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pandu_jati_db');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Gagal terhubung ke database: ' . $e->getMessage()
    ]);
    exit;
}

// Informasi Bisnis Global
$business_config = [
    'brand_name'      => 'Pandu Jati Furniture',
    'legal_name'      => 'CV. Pandu Jati Furniture Jepara',
    'tagline'         => 'Furniture Jati Berkualitas untuk Rumah Impian Anda',
    'phone'           => '+62 851-9123-5955',
    'whatsapp'        => '6285191235955',
    'email'           => 'info@pandujatifurniture.com',
    'address'         => 'Jl. Raya Tahunan - Batealit KM 3.5, Sentra Industri Mebel Kayu Jati, Jepara, Jawa Tengah 59427',
    'operating_hours' => 'Senin - Sabtu: 08:00 - 17:00 WIB (Konsultasi Online 24 Jam)',
    'bank_accounts'   => [
        ['bank' => 'BCA', 'number' => '1234-567-890', 'holder' => 'Pandu Jati Furniture'],
        ['bank' => 'Mandiri', 'number' => '138-00-1234567-8', 'holder' => 'Pandu Jati Furniture'],
        ['bank' => 'BRI', 'number' => '0023-01-001234-50-8', 'holder' => 'Pandu Jati Furniture']
    ],
    'shipping_options' => [
        ['id' => 'truk-jepara', 'name' => 'Truk Ekspedisi Khusus Mebel Jepara', 'cost' => 250000, 'time' => '2 - 5 Hari Kerja'],
        ['id' => 'kargo-crate', 'name' => 'Kargo Darat/Laut + Peti Kayu Solid', 'cost' => 450000, 'time' => '5 - 10 Hari Kerja'],
        ['id' => 'pickup', 'name' => 'Ambil Sendiri di Workshop (Jepara)', 'cost' => 0, 'time' => 'Sesuai kesepakatan']
    ]
];

/**
 * Helper: Mengirim respon JSON standar ke frontend
 */
function json_response($success, $data_or_message, $status_code = 200) {
    http_response_code($status_code);
    if ($success) {
        echo json_encode([
            'success' => true,
            'data'    => $data_or_message
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } else {
        echo json_encode([
            'success' => false,
            'message' => $data_or_message
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    exit;
}

/**
 * Helper: Membaca input JSON dari frontend JS/TS (fetch API)
 */
function get_json_input() {
    $raw = file_get_contents('php://input');
    if (empty($raw)) {
        return $_POST;
    }
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : $_POST;
}

/**
 * Helper: Verifikasi apakah session admin sedang aktif
 */
function require_admin() {
    if (empty($_SESSION['admin_logged_in'])) {
        json_response(false, 'Akses ditolak. Harap login sebagai administrator terlebih dahulu.', 401);
    }
}
