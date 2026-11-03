<?php
/**
 * ============================================================================
 * ENDPOINT API: PRODUK FURNITURE (CRUD)
 * ============================================================================
 * GET    : Mengambil daftar produk atau detail satu produk berdasarkan ID / slug
 * POST   : Menambah produk mebel baru (Admin)
 * PUT    : Mengubah data produk mebel (Admin)
 * DELETE : Menghapus produk mebel (Admin)
 */

require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];

// -----------------------------------------------------------------------------
// 1. GET: Ambil Produk
// -----------------------------------------------------------------------------
if ($method === 'GET') {
    // A. Detail satu produk berdasarkan ID atau slug
    if (isset($_GET['id']) || isset($_GET['slug'])) {
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug 
                                   FROM products p 
                                   LEFT JOIN categories c ON p.category_id = c.id 
                                   WHERE p.id = ?");
            $stmt->execute([(int)$_GET['id']]);
        } else {
            $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug 
                                   FROM products p 
                                   LEFT JOIN categories c ON p.category_id = c.id 
                                   WHERE p.slug = ?");
            $stmt->execute([trim($_GET['slug'])]);
        }
        
        $product = $stmt->fetch();
        if (!$product) {
            json_response(false, 'Produk mebel tidak ditemukan.', 404);
        }

        // Ambil produk terkait dalam kategori yang sama
        $rel_stmt = $pdo->prepare("SELECT id, name, slug, price, original_price, image_url 
                                   FROM products 
                                   WHERE category_id = ? AND id != ? AND is_active = 1 
                                   LIMIT 4");
        $rel_stmt->execute([$product['category_id'], $product['id']]);
        $related = $rel_stmt->fetchAll();

        json_response(true, [
            'product' => $product,
            'related' => $related
        ]);
    }

    // B. Ambil daftar produk dengan pencarian & filter
    $search   = trim($_GET['search'] ?? '');
    $category = trim($_GET['category'] ?? 'all');
    $sort     = trim($_GET['sort'] ?? 'terbaru');
    $featured = isset($_GET['featured']) ? (int)$_GET['featured'] : null;

    $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_active = 1";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.material LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if (!empty($category) && $category !== 'all') {
        $sql .= " AND (c.slug = ? OR c.name = ?)";
        $params[] = $category;
        $params[] = $category;
    }

    if ($featured !== null) {
        $sql .= " AND p.is_featured = ?";
        $params[] = $featured;
    }

    // Sorting
    switch ($sort) {
        case 'termurah':
            $sql .= " ORDER BY p.price ASC";
            break;
        case 'termahal':
            $sql .= " ORDER BY p.price DESC";
            break;
        default:
            $sql .= " ORDER BY p.id DESC";
            break;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    // Ambil semua daftar kategori untuk tombol filter frontend
    $cat_stmt = $pdo->query("SELECT id, name, slug, description FROM categories ORDER BY id ASC");
    $categories = $cat_stmt->fetchAll();

    json_response(true, [
        'total'      => count($products),
        'products'   => $products,
        'categories' => $categories
    ]);
}

// -----------------------------------------------------------------------------
// 2. POST: Tambah Produk Baru
// -----------------------------------------------------------------------------
if ($method === 'POST') {
    require_admin();
    $data = get_json_input();

    $name        = trim($data['name'] ?? '');
    $category_id = (int)($data['category_id'] ?? 1);
    $price       = (float)($data['price'] ?? 0);
    $orig_price  = !empty($data['original_price']) ? (float)$data['original_price'] : null;
    $stock       = (int)($data['stock'] ?? 5);
    $material    = trim($data['material'] ?? 'Kayu Jati Solid TPK Perhutani');
    $dimensions  = trim($data['dimensions'] ?? 'P 180 x L 90 x T 75 cm');
    $weight      = trim($data['weight'] ?? '50 kg');
    $finishing   = trim($data['finishing'] ?? 'Natural Teak Melamine Doff');
    $lead_time   = trim($data['lead_time'] ?? '7 - 14 Hari Kerja');
    $image_url   = trim($data['image_url'] ?? '');
    $description = trim($data['description'] ?? '');
    $is_featured = !empty($data['is_featured']) ? 1 : 0;

    if (empty($name) || $price <= 0 || empty($image_url)) {
        json_response(false, 'Nama produk, harga, dan foto produk wajib diisi.', 400);
    }

    // Buat slug otomatis dari nama
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $check = $pdo->prepare("SELECT id FROM products WHERE slug = ?");
    $check->execute([$slug]);
    if ($check->fetch()) {
        $slug .= '-' . time();
    }

    $ins = $pdo->prepare("INSERT INTO products 
        (category_id, name, slug, price, original_price, description, material, dimensions, weight, finishing, lead_time, stock, image_url, is_featured, is_active) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
    
    $ins->execute([
        $category_id, $name, $slug, $price, $orig_price, $description,
        $material, $dimensions, $weight, $finishing, $lead_time, $stock,
        $image_url, $is_featured
    ]);

    json_response(true, [
        'id'      => $pdo->lastInsertId(),
        'message' => 'Produk mebel berhasil ditambahkan!'
    ], 201);
}

// -----------------------------------------------------------------------------
// 3. PUT: Edit Produk
// -----------------------------------------------------------------------------
if ($method === 'PUT') {
    require_admin();
    $data = get_json_input();
    $id = (int)($data['id'] ?? 0);

    if ($id <= 0) {
        json_response(false, 'ID produk tidak valid.', 400);
    }

    $upd = $pdo->prepare("UPDATE products SET 
        name = ?, category_id = ?, price = ?, original_price = ?, stock = ?, 
        material = ?, dimensions = ?, weight = ?, finishing = ?, lead_time = ?, 
        image_url = ?, description = ?, is_featured = ? 
        WHERE id = ?");

    $upd->execute([
        trim($data['name']),
        (int)$data['category_id'],
        (float)$data['price'],
        !empty($data['original_price']) ? (float)$data['original_price'] : null,
        (int)$data['stock'],
        trim($data['material']),
        trim($data['dimensions']),
        trim($data['weight']),
        trim($data['finishing']),
        trim($data['lead_time']),
        trim($data['image_url']),
        trim($data['description']),
        !empty($data['is_featured']) ? 1 : 0,
        $id
    ]);

    json_response(true, ['message' => 'Produk berhasil diperbarui!']);
}

// -----------------------------------------------------------------------------
// 4. DELETE: Hapus Produk
// -----------------------------------------------------------------------------
if ($method === 'DELETE') {
    require_admin();
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
        json_response(false, 'ID produk tidak valid.', 400);
    }

    $del = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $del->execute([$id]);

    json_response(true, ['message' => 'Produk berhasil dihapus.']);
}

json_response(false, 'Metode HTTP tidak didukung.', 405);
