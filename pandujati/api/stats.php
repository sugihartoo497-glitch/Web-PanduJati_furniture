<?php
/**
 * ============================================================================
 * ENDPOINT API: STATISTIK DASHBOARD ADMIN (KPI)
 * ============================================================================
 * GET /api/stats.php
 * Mengembalikan jumlah total pesanan, pesanan baru, sedang produksi, selesai,
 * dan total pendapatan.
 */

require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(false, 'Metode HTTP tidak didukung.', 405);
}

require_admin();

$total_orders     = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$new_orders       = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'PENDING'")->fetchColumn();
$prod_orders      = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'PRODUCTION'")->fetchColumn();
$completed_orders = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'COMPLETED'")->fetchColumn();
$total_revenue    = (float)$pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE payment_status IN ('PAID', 'DP')")->fetchColumn();

// 5 Pesanan Terbaru
$recent_orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5")->fetchAll();

json_response(true, [
    'total_orders'     => $total_orders,
    'new_orders'       => $new_orders,
    'prod_orders'      => $prod_orders,
    'completed_orders' => $completed_orders,
    'total_revenue'    => $total_revenue,
    'recent_orders'    => $recent_orders
]);
