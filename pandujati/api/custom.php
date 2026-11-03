<?php
/**
 * ============================================================================
 * ENDPOINT API: CUSTOM FURNITURE INQUIRIES
 * ============================================================================
 * POST : Pelanggan mengirim formulir permohonan custom mebel kayu
 * GET  : Admin melihat daftar permohonan custom
 */

require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];

// 1. POST: Kirim Request Custom oleh Pelanggan
if ($method === 'POST') {
    $data = get_json_input();

    $customer_name   = trim($data['customer_name'] ?? '');
    $customer_phone  = trim($data['customer_phone'] ?? '');
    $customer_email  = trim($data['customer_email'] ?? '');
    $furniture_type  = trim($data['furniture_type'] ?? '');
    $dimensions      = trim($data['dimensions'] ?? '');
    $wood_type       = trim($data['wood_type'] ?? 'Kayu Jati Solid TPK Perhutani Grade A');
    $finishing       = trim($data['finishing'] ?? 'Natural Teak Melamine Doff');
    $budget_range    = trim($data['budget_range'] ?? '');
    $description     = trim($data['description'] ?? '');
    $reference_image = trim($data['reference_image'] ?? '');

    if (empty($customer_name) || empty($customer_phone) || empty($furniture_type) || empty($description)) {
        json_response(false, 'Nama, nomor WhatsApp, jenis furniture, dan deskripsi kebutuhan wajib diisi.', 400);
    }

    $datePart = date('Ymd');
    $random = mt_rand(1000, 9999);
    $request_number = "PJF-CST-{$datePart}-{$random}";

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

        $wa_msg = "Halo Pandu Jati Furniture,\nSaya baru saja mengirimkan Request Custom Mebel.\n\n"
                . "Nomor Tiket: {$request_number}\n"
                . "Nama: {$customer_name}\n"
                . "Jenis Mebel: {$furniture_type}\n"
                . "Bahan Kayu: {$wood_type}\n"
                . "Ukuran: " . ($dimensions ?: '-') . "\n"
                . "Estimasi Budget: " . ($budget_range ?: '-') . "\n\n"
                . "Mohon estimasi penawaran harganya. Terima kasih.";

        json_response(true, [
            'request_number'  => $request_number,
            'customer_name'   => $customer_name,
            'furniture_type'  => $furniture_type,
            'wood_type'       => $wood_type,
            'whatsapp_cs_url' => "https://wa.me/{$business_config['whatsapp']}?text=" . urlencode($wa_msg)
        ], 201);
    } catch (Exception $e) {
        json_response(false, 'Gagal menyimpan request: ' . $e->getMessage(), 500);
    }
}

// 2. GET: Daftar Request Custom untuk Admin
if ($method === 'GET') {
    require_admin();
    $stmt = $pdo->query("SELECT * FROM custom_requests ORDER BY id DESC");
    $requests = $stmt->fetchAll();

    json_response(true, [
        'total'    => count($requests),
        'requests' => $requests
    ]);
}

json_response(false, 'Metode HTTP tidak didukung.', 405);
