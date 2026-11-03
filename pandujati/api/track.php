<?php
/**
 * ============================================================================
 * ENDPOINT API: PELACAKAN PESANAN (ORDER TRACKING 8-TAHAP)
 * ============================================================================
 * GET /api/track.php?order_number=PJF-20261008-0001
 * Mengembalikan informasi status pesanan, rincian barang, dan timeline 8 tahap
 */

require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(false, 'Metode HTTP tidak didukung.', 405);
}

$order_number = trim($_GET['order_number'] ?? '');

if (empty($order_number)) {
    json_response(false, 'Nomor pesanan wajib disertakan.', 400);
}

// Cari pesanan berdasarkan Order ID unik
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ? LIMIT 1");
$stmt->execute([$order_number]);
$order = $stmt->fetch();

if (!$order) {
    json_response(false, "Nomor pesanan '{$order_number}' tidak ditemukan dalam sistem kami.", 404);
}

// Ambil daftar barang pesanan
$item_stmt = $pdo->prepare("SELECT oi.*, p.image_url 
                            FROM order_items oi 
                            LEFT JOIN products p ON oi.product_id = p.id 
                            WHERE oi.order_id = ?");
$item_stmt->execute([$order['id']]);
$items = $item_stmt->fetchAll();

// Ambil riwayat log perubahan status
$hist_stmt = $pdo->prepare("SELECT * FROM order_status_history WHERE order_id = ? ORDER BY id ASC");
$hist_stmt->execute([$order['id']]);
$history = $hist_stmt->fetchAll();

// Definisi 8-Tahap Produksi Mebel Kayu Jati
$stages = [
    [
        'step'        => 1,
        'code'        => 'PENDING',
        'title'       => 'Pesanan Dibuat',
        'description' => 'Pesanan telah masuk sistem, menunggu konfirmasi DP 50% atau pelunasan.'
    ],
    [
        'step'        => 2,
        'code'        => 'CONFIRMED',
        'title'       => 'Pesanan Dikonfirmasi',
        'description' => 'Pembayaran terverifikasi, jadwal antrian produksi bengkel telah ditentukan.'
    ],
    [
        'step'        => 3,
        'code'        => 'PROCESSING',
        'title'       => 'Sedang Diproses',
        'description' => 'Pemilihan kayu jati solid TPK Perhutani & proses pengeringan oven (Kiln Dried).'
    ],
    [
        'step'        => 4,
        'code'        => 'PRODUCTION',
        'title'       => 'Sedang Dibuat (Produksi)',
        'description' => 'Pemotongan presisi, pembuatan purus-pasak, pengukiran, dan perakitan konstruksi mebel.'
    ],
    [
        'step'        => 5,
        'code'        => 'QUALITY_CHECK',
        'title'       => 'Quality Check',
        'description' => 'Pemeriksaan kehalusan finishing, kekokohan struktur, dan pembersihan mebel.'
    ],
    [
        'step'        => 6,
        'code'        => 'READY_TO_SHIP',
        'title'       => 'Siap Dikirim',
        'description' => 'Mebel dibungkus busa foam sheet tebal, single face, dan rangka packing peti kayu solid.'
    ],
    [
        'step'        => 7,
        'code'        => 'SHIPPED',
        'title'       => 'Dalam Pengiriman',
        'description' => 'Pesanan diangkut truk ekspedisi kargo khusus mebel Jepara menuju kota tujuan.'
    ],
    [
        'step'        => 8,
        'code'        => 'COMPLETED',
        'title'       => 'Pesanan Selesai',
        'description' => 'Mebel kayu jati telah sampai di rumah pelanggan dengan aman dan memuaskan.'
    ]
];

// Cari index posisi langkah saat ini (0 sampai 7, atau -1 jika CANCELLED)
$current_index = 0;
if ($order['order_status'] === 'CANCELLED') {
    $current_index = -1;
} else {
    foreach ($stages as $idx => $stg) {
        if ($stg['code'] === $order['order_status']) {
            $current_index = $idx;
            break;
        }
    }
}

// Kirimkan response
json_response(true, [
    'order'               => $order,
    'items'               => $items,
    'history'             => $history,
    'stages'              => $stages,
    'current_stage_index' => $current_index,
    'whatsapp_cs_url'     => "https://wa.me/{$business_config['whatsapp']}?text=" . urlencode("Halo Pandu Jati, saya ingin menanyakan perkembangan Nomor Pesanan *{$order['order_number']}*")
]);
