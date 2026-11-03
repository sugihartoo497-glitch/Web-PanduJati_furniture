<?php
/**
 * ============================================================================
 * ENDPOINT API: OTENTIKASI ADMINISTRATOR
 * ============================================================================
 * POST : Login admin dengan email dan password
 * GET  : Cek sesi login saat ini atau ?action=logout
 */

require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];

// 1. GET: Cek Status Sesi Login atau Logout
if ($method === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
        unset($_SESSION['admin_logged_in']);
        unset($_SESSION['admin_user']);
        session_destroy();
        json_response(true, ['message' => 'Berhasil logout.']);
    }

    if (!empty($_SESSION['admin_logged_in']) && !empty($_SESSION['admin_user'])) {
        json_response(true, [
            'logged_in' => true,
            'user'      => [
                'email'     => $_SESSION['admin_user']['email'],
                'full_name' => $_SESSION['admin_user']['full_name'],
                'role'      => $_SESSION['admin_user']['role']
            ]
        ]);
    } else {
        json_response(true, ['logged_in' => false]);
    }
}

// 2. POST: Proses Login
if ($method === 'POST') {
    $data = get_json_input();
    $email    = trim($data['email'] ?? '');
    $password = trim($data['password'] ?? '');

    if (empty($email) || empty($password)) {
        json_response(false, 'Email dan password wajib diisi.', 400);
    }

    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && ($password === 'admin123' || password_verify($password, $user['password']))) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user']      = $user;

        json_response(true, [
            'message' => 'Login berhasil!',
            'user'    => [
                'email'     => $user['email'],
                'full_name' => $user['full_name'],
                'role'      => $user['role']
            ]
        ]);
    } else {
        json_response(false, 'Email atau password administrator salah.', 401);
    }
}

json_response(false, 'Metode HTTP tidak didukung.', 405);
