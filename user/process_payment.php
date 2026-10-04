<?php

session_start();

require_once "../bootstrap.php";

$db = new DBconnection();

// Ambil data
$event_id = (int) ($_POST['event_id'] ?? 0);

$tickets = $_POST['tickets'] ?? [];

$customer_name =
    trim($_POST['customer_name'] ?? '');

$customer_email =
    trim($_POST['customer_email'] ?? '');

$customer_phone =
    trim($_POST['customer_phone'] ?? '');

$payment_method =
    $_POST['payment_method'] ?? '';

// Validasi
if (
    $event_id <= 0 ||
    empty($tickets) ||
    $customer_name === '' ||
    $customer_email === '' ||
    $customer_phone === '' ||
    !in_array(
        $payment_method,
        [
            'transfer_bank',
            'e_wallet',
            'qris'
        ]
    )
) {
    header("Location: index.php");
    exit;
}

// Proses transaksi
$transaction = new Transaction($db);

$result = $transaction->checkout(
    $event_id,
    $tickets,
    $customer_name,
    $customer_email,
    $customer_phone,
    $payment_method
);

// Jika gagal
if (!$result->success) {
    die(
        "Transaksi gagal: " .
        htmlspecialchars($result->message)
    );
}

// Data transaksi berhasil
$order = $result->data[0];

// Simpan untuk halaman sukses
$_SESSION['order_success'] = [

    'order_id' =>
        $order['order_id'],

    'order_code' =>
        $order['order_code'],

    'customer_name' =>
        $customer_name,

    'customer_email' =>
        $customer_email,

    'customer_phone' =>
        $customer_phone,

    'total_amount' =>
        $order['total_amount'],

    'payment_method' =>
        $payment_method
];

header("Location: order_success.php");
exit;
