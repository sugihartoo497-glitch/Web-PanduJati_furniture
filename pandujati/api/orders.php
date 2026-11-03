<?php
/**
 * ============================================================================
 * ENDPOINT API: ORDER & CHECKOUT SYSTEM
 * ============================================================================
 * POST : Pelanggan membuat pesanan baru (menghasilkan Order ID unik PJF-...)
 * GET  : Admin melihat daftar semua pesanan
 * PUT  : Admin memperbarui status pesanan (8-tahap produksi) & pembayaran
 */

require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];

// -----------------------------------------------------------------------------
// 1. POST: Buat Pesanan Baru oleh Pelanggan
// -----------------------------------------------------------------------------
if ($method === 'POST') {
    $data = get_json_input();

    $customer_name   = trim($data['customer_name'] ?? '');
    $customer_phone  = trim($data['customer_phone'] ?? '');
    $customer_email  = trim($data['customer_email'] ?? '');
    $address         = trim($data['address'] ?? '');
    $city            = trim($data['city'] ?? '');
    $province        = trim($data['province'] ?? '');
    $postal_code     = trim($data['postal_code'] ?? '');
    $product_id      = (int)($data['product_id'] ?? 0);
    $quantity        = max(1, (int)($data['quantity'] ?? 1));
    $finishing       = trim($data['finishing_choice'] ?? 'Natural Teak Melamine Doff');
    $shipping_idx    = (int)($data['shipping_option_index'] ?? 0);
    $payment_term    = trim($data['payment_term'] ?? 'Transfer Bank BCA (DP 50%)');
    $customer_notes  = trim($data['customer_notes'] ?? '');

    // Validasi field wajib
    if (empty($customer_name) || empty($customer_phone) || empty($address) || empty($city)) {
        json_response(false, 'Harap lengkapi nama, nomor WhatsApp, dan alamat pengiriman Anda.', 400);
    }

    if ($product_id <= 0) {
        json_response(false, 'Produk mebel yang dipilih tidak valid.', 400);
    }

    // Ambil data produk dari database
    $p_stmt = $pdo->prepare("SELECT id, name, price, stock, image_url FROM products WHERE id = ? AND is_active = 1");
    $p_stmt->execute([$product_id]);
    $product = $p_stmt->fetch();

    if (!$product) {
        json_response(false, 'Produk mebel tidak ditemukan atau stok sedang kosong.', 404);
    }

    // Kalkulasi Biaya
    $selected_shipping = $business_config['shipping_options'][$shipping_idx] ?? $business_config['shipping_options'][0];
    $shipping_cost = (float)$selected_shipping['cost'];
    $shipping_name = $selected_shipping['name'];

    $subtotal = $product['price'] * $quantity;
    $total_amount = $subtotal + $shipping_cost;

    // Generator Nomor Pesanan Unik: PJF-YYYYMMDD-XXXX
    $datePart = date('Ymd');
    $random = mt_rand(1000, 9999);
    $order_number = "PJF-{$datePart}-{$random}";

    // Simpan ke database dengan Database Transaction
    try {
        $pdo->beginTransaction();

        $ins_order = $pdo->prepare("INSERT INTO orders 
            (order_number, customer_name, customer_phone, customer_email, address, city, province, postal_code, 
             shipping_method, shipping_cost, payment_method, subtotal, total_amount, order_status, payment_status, customer_notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'PENDING', 'UNPAID', ?)");

        $ins_order->execute([
            $order_number,
            $customer_name,
            $customer_phone,
            $customer_email,
            $address,
            $city,
            $province,
            $postal_code,
            $shipping_name,
            $shipping_cost,
            $payment_term,
            $subtotal,
            $total_amount,
            $customer_notes
        ]);

        $order_id = $pdo->lastInsertId();

        // Masukkan Rincian Item Pesanan
        $ins_item = $pdo->prepare("INSERT INTO order_items 
            (order_id, product_id, product_name, product_price, quantity, finishing_choice, subtotal) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $ins_item->execute([
            $order_id,
            $product['id'],
            $product['name'],
            $product['price'],
            $quantity,
            $finishing,
            $subtotal
        ]);

        // Catat riwayat status pertama kali
        $ins_hist = $pdo->prepare("INSERT INTO order_status_history (order_id, status, note) VALUES (?, 'PENDING', 'Pesanan online berhasil dibuat oleh pelanggan.')");
        $ins_hist->execute([$order_id]);

        $pdo->commit();

        // Format Pesan WhatsApp Otomatis
        $wa_text = "Halo Pandu Jati Furniture,\nSaya ingin melakukan pemesanan.\n\n"
                 . "Nomor Pesanan: {$order_number}\n"
                 . "Nama: {$customer_name}\n"
                 . "Produk: {$product['name']} ({$quantity}x)\n"
                 . "Finishing: {$finishing}\n"
                 . "Total: Rp " . number_format($total_amount, 0, ',', '.') . "\n"
                 . "Alamat: {$address}, {$city}, {$province}\n\n"
                 . "Mohon dikonfirmasi. Terima kasih.";

        json_response(true, [
            'order_number'    => $order_number,
            'customer_name'   => $customer_name,
            'customer_phone'  => $customer_phone,
            'product_name'    => $product['name'],
            'quantity'        => $quantity,
            'subtotal'        => $subtotal,
            'shipping_cost'   => $shipping_cost,
            'total_amount'    => $total_amount,
            'payment_method'  => $payment_term,
            'shipping_method' => $shipping_name,
            'bank_accounts'   => $business_config['bank_accounts'],
            'whatsapp_url'    => "https://wa.me/{$business_config['whatsapp']}?text=" . urlencode($wa_text)
        ], 201);

    } catch (Exception $e) {
        $pdo->rollBack();
        json_response(false, 'Gagal menyimpan pesanan: ' . $e->getMessage(), 500);
    }
}

// -----------------------------------------------------------------------------
// 2. GET: Ambil Daftar Pesanan (Untuk Admin Dashboard)
// -----------------------------------------------------------------------------
if ($method === 'GET') {
    require_admin();

    $stmt = $pdo->query("SELECT o.*, 
                         (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as total_items,
                         (SELECT GROUP_CONCAT(CONCAT(product_name, ' (', quantity, 'x)') SEPARATOR ', ') FROM order_items WHERE order_id = o.id) as items_summary
                         FROM orders o 
                         ORDER BY o.id DESC");
    $orders = $stmt->fetchAll();

    json_response(true, [
        'total'  => count($orders),
        'orders' => $orders
    ]);
}

// -----------------------------------------------------------------------------
// 3. PUT: Update Status Pesanan (8-Tahap) & Pembayaran oleh Admin
// -----------------------------------------------------------------------------
if ($method === 'PUT') {
    require_admin();
    $data = get_json_input();

    $order_id       = (int)($data['order_id'] ?? 0);
    $order_status   = trim($data['order_status'] ?? '');
    $payment_status = trim($data['payment_status'] ?? '');
    $tracking_num   = trim($data['tracking_number'] ?? '');
    $admin_notes    = trim($data['admin_notes'] ?? '');

    if ($order_id <= 0 || empty($order_status) || empty($payment_status)) {
        json_response(false, 'ID pesanan, status pesanan, dan status pembayaran wajib diisi.', 400);
    }

    try {
        $pdo->beginTransaction();

        $upd = $pdo->prepare("UPDATE orders SET 
            order_status = ?, 
            payment_status = ?, 
            tracking_number = ?, 
            admin_notes = ?,
            updated_at = NOW() 
            WHERE id = ?");
        
        $upd->execute([$order_status, $payment_status, $tracking_num, $admin_notes, $order_id]);

        // Simpan ke riwayat timeline
        $hist = $pdo->prepare("INSERT INTO order_status_history (order_id, status, note) VALUES (?, ?, ?)");
        $note = "Status diubah menjadi: {$order_status}. " . ($admin_notes ? "Catatan: {$admin_notes}" : "");
        $hist->execute([$order_id, $order_status, $note]);

        $pdo->commit();

        json_response(true, ['message' => 'Status pesanan berhasil diperbarui!']);
    } catch (Exception $e) {
        $pdo->rollBack();
        json_response(false, 'Gagal memperbarui status: ' . $e->getMessage(), 500);
    }
}

json_response(false, 'Metode HTTP tidak didukung.', 405);
