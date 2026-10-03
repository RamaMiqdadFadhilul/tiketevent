<?php

session_start();

require_once "../bootstrap.php";

$db = new DBconnection();

// Ambil data dari form
$event_id = (int) ($_POST['event_id'] ?? 0);
$ticket_quantities = $_POST['tickets'] ?? [];

$customer_name = trim($_POST['customer_name'] ?? '');
$customer_email = trim($_POST['customer_email'] ?? '');
$customer_phone = trim($_POST['customer_phone'] ?? '');

$payment_method = $_POST['payment_method'] ?? '';

// Validasi data
if (
    $event_id <= 0 ||
    empty($ticket_quantities) ||
    $customer_name === '' ||
    $customer_email === '' ||
    $customer_phone === '' ||
    !in_array($payment_method, [
        'transfer_bank',
        'e_wallet',
        'qris'
    ])
) {
    header("Location: index.php");
    exit;
}


// ===============================
// BERSIHKAN TIKET YANG DIPILIH
// ===============================

$selected_tickets = [];

foreach ($ticket_quantities as $ticket_id => $quantity) {

    $ticket_id = (int) $ticket_id;
    $quantity = (int) $quantity;

    if ($ticket_id > 0 && $quantity > 0) {
        $selected_tickets[$ticket_id] = $quantity;
    }
}

if (empty($selected_tickets)) {
    header(
        "Location: select_ticket.php?event_id=" .
        $event_id
    );
    exit;
}


// ===============================
// AMBIL DATA TIKET DARI DATABASE
// ===============================

$ticket_ids = array_keys($selected_tickets);

$placeholders = [];
$params = [
    'event_id' => $event_id
];

foreach ($ticket_ids as $index => $ticket_id) {

    $key = "ticket_" . $index;

    $placeholders[] = ":" . $key;
    $params[$key] = $ticket_id;
}

$result = $db->send_query(
    "SELECT *
     FROM tickets
     WHERE event_id = :event_id
     AND id IN (" . implode(',', $placeholders) . ")
     ORDER BY price",
    $params
);

if (!$result->success || empty($result->data)) {
    die("Data tiket tidak ditemukan.");
}

$tickets = $result->data;


// ===============================
// CEK STOK & HITUNG TOTAL
// ===============================

$total_amount = 0;

foreach ($tickets as $ticket) {

    $quantity = $selected_tickets[$ticket['id']] ?? 0;

    if ($quantity > $ticket['stock']) {
        die(
            "Stok tiket " .
            htmlspecialchars($ticket['name']) .
            " tidak mencukupi."
        );
    }

    $total_amount += $ticket['price'] * $quantity;
}


// ===============================
// BUAT KODE ORDER
// ===============================

$order_code =
    'ORD-' .
    date('YmdHis') .
    '-' .
    rand(100, 999);


// ===============================
// INSERT ORDER
// ===============================

$order_result = $db->send_query(
    "INSERT INTO orders
        (
            order_code,
            customer_name,
            customer_email,
            customer_phone,
            total_amount,
            status
        )
     VALUES
        (
            :order_code,
            :customer_name,
            :customer_email,
            :customer_phone,
            :total_amount,
            'paid'
        )
     RETURNING *",
    [
        'order_code' => $order_code,
        'customer_name' => $customer_name,
        'customer_email' => $customer_email,
        'customer_phone' => $customer_phone,
        'total_amount' => $total_amount
    ]
);

if (!$order_result->success) {
    die(
        "Gagal membuat order: " .
        $order_result->message
    );
}

$order = $order_result->data[0];

$order_id = $order['id'];


// ===============================
// INSERT ORDER ITEMS
// ===============================

foreach ($tickets as $ticket) {

    $quantity = $selected_tickets[$ticket['id']];

    $price = $ticket['price'];

    $subtotal = $price * $quantity;


    $item_result = $db->send_query(
        "INSERT INTO order_items
            (
                order_id,
                ticket_id,
                quantity,
                price,
                subtotal
            )
         VALUES
            (
                :order_id,
                :ticket_id,
                :quantity,
                :price,
                :subtotal
            )",
        [
            'order_id' => $order_id,
            'ticket_id' => $ticket['id'],
            'quantity' => $quantity,
            'price' => $price,
            'subtotal' => $subtotal
        ]
    );

    if (!$item_result->success) {
        die(
            "Gagal menyimpan detail order: " .
            $item_result->message
        );
    }


    // ===============================
    // KURANGI STOK
    // ===============================

    $stock_result = $db->send_query(
        "UPDATE tickets
         SET stock = stock - :quantity
         WHERE id = :ticket_id",
        [
            'quantity' => $quantity,
            'ticket_id' => $ticket['id']
        ]
    );

    if (!$stock_result->success) {
        die(
            "Gagal mengurangi stok: " .
            $stock_result->message
        );
    }
}


// ===============================
// INSERT PAYMENT
// ===============================

$payment_result = $db->send_query(
    "INSERT INTO payments
        (
            order_id,
            payment_method,
            amount,
            status,
            paid_at
        )
     VALUES
        (
            :order_id,
            :payment_method,
            :amount,
            'paid',
            CURRENT_TIMESTAMP
        )",
    [
        'order_id' => $order_id,
        'payment_method' => $payment_method,
        'amount' => $total_amount
    ]
);

if (!$payment_result->success) {
    die(
        "Gagal menyimpan pembayaran: " .
        $payment_result->message
    );
}


// ===============================
// SIMPAN DATA UNTUK HALAMAN SELESAI
// ===============================

$_SESSION['order_success'] = [
    'order_id' => $order_id,
    'order_code' => $order_code,
    'customer_name' => $customer_name,
    'customer_email' => $customer_email,
    'customer_phone' => $customer_phone,
    'total_amount' => $total_amount,
    'payment_method' => $payment_method
];

header("Location: order_success.php");
exit;
