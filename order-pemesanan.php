<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['customer_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $payment = $_POST['payment_method'];
    $cart_data = json_decode($_POST['cart_data'], true);

    $total = 0;
    foreach ($cart_data as $item) {
        $total += $item['price'];
    }

    $query = "INSERT INTO orders (customer_name, phone, address, payment_method, total_amount) VALUES ('$name', '$phone', '$address', '$payment', '$total')";
    if ($conn->query($query)) {
        echo "<script>alert('Pesanan Berhasil Dibuat!'); window.location.href='index.php';</script>";
    } else {
        echo "Gagal memproses pesanan: " . $conn->error;
    }
}
?>