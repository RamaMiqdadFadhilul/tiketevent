<?php

require_once "bootstrap.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Request tidak valid.');
}

$customerName = $_POST['customer_name'];
$customerEmail = $_POST['customer_email'];
$customerPhone = $_POST['customer_phone'];

$ticketId = (int) $_POST['ticket_id'];
$quantity = (int) $_POST['quantity'];

if ($quantity <= 0) {
    die('Jumlah tiket tidak valid.');
}

$db = new DBconnection();

$ticketModel = new Ticket($db);

$ticket = $ticketModel->find_by_id($ticketId);

if ($ticket === null) {
    die('Tiket tidak ditemukan.');
}

if ($quantity > $ticket['stock']) {
    die('Stok tiket tidak mencukupi.');
}

$price = (float) $ticket['price'];

$subtotal = $price * $quantity;

$totalAmount = $subtotal;

$orderModel = new Order($db);

$orderCode = 'ORD-' . date('YmdHis') . '-' . random_int(100, 999);

$orderData = [
    'order_code' => $orderCode,
    'customer_name' => $customerName,
    'customer_email' => $customerEmail,
    'customer_phone' => $customerPhone,
    'total_amount' => $totalAmount,
    'status' => 'pending'
];

$orderResponse = $orderModel->insert($orderData);

echo "<h1>Data Pesanan</h1>";

echo "Nama: " . htmlspecialchars($customerName) . "<br>";
echo "Email: " . htmlspecialchars($customerEmail) . "<br>";
echo "No HP: " . htmlspecialchars($customerPhone) . "<br>";
echo "Tiket: " . htmlspecialchars($ticket['name']) . "<br>";
echo "Harga: Rp" . number_format($price, 0, ',', '.') . "<br>";
echo "Jumlah: " . $quantity . "<br>";
echo "Total: Rp" . number_format($totalAmount, 0, ',', '.');