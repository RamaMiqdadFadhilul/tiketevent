<?php

session_start();

require_once "../bootstrap.php";

$db = new DBconnection();

$event_id = (int) ($_POST['event_id'] ?? 0);
$ticket_quantities = $_POST['tickets'] ?? [];

$customer_name = trim($_POST['customer_name'] ?? '');
$customer_email = trim($_POST['customer_email'] ?? '');
$customer_phone = trim($_POST['customer_phone'] ?? '');

// ===============================
// PROSES BAYAR
// ===============================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_method'])) {

    $payment_method = $_POST['payment_method'];

    if (
        $event_id <= 0 ||
        empty($ticket_quantities) ||
        $customer_name === '' ||
        $customer_email === '' ||
        $customer_phone === ''
    ) {
        header("Location: index.php");
        exit;
    }

    // Ambil ticket yang dipilih dari database
    $ticket_ids = [];

    foreach ($ticket_quantities as $ticket_id => $quantity) {
        $ticket_id = (int) $ticket_id;
        $quantity = (int) $quantity;

        if ($ticket_id > 0 && $quantity > 0) {
            $ticket_ids[$ticket_id] = $quantity;
        }
    }

    if (empty($ticket_ids)) {
        header(
            "Location: select_ticket.php?event_id=" .
            $event_id
        );
        exit;
    }

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
         AND id IN (" . implode(',', $placeholders) . ")",
        $params
    );

    if (!$result->success || empty($result->data)) {
        die("Data tiket tidak ditemukan.");
    }

    $tickets = $result->data;

    // ===============================
    // HITUNG TOTAL
    // ===============================

    $total_amount = 0;

    foreach ($tickets as $ticket) {

        $quantity = $ticket_ids[$ticket['id']] ?? 0;

        // Cek stok
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
    // BUAT ORDER CODE
    // ===============================

    $order_code = 'ORD-' . date('YmdHis') . '-' . rand(100, 999);

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
                'pending'
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
        die("Gagal membuat order: " . $order_result->message);
    }

    $order = $order_result->data[0];
    $order_id = $order['id'];

    // ===============================
    // INSERT ORDER ITEMS
    // ===============================

    foreach ($tickets as $ticket) {

        $quantity = $ticket_ids[$ticket['id']];
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
                status
            )
         VALUES
            (
                :order_id,
                :payment_method,
                :amount,
                'paid'
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
    // UPDATE STATUS ORDER
    // ===============================

    $update_order = $db->send_query(
        "UPDATE orders
         SET status = 'paid'
         WHERE id = :id",
        [
            'id' => $order_id
        ]
    );

    if (!$update_order->success) {
        die(
            "Gagal mengubah status order: " .
            $update_order->message
        );
    }

    // Simpan informasi order ke session
    $_SESSION['order_success'] = [
        'order_id' => $order_id,
        'order_code' => $order_code,
        'customer_name' => $customer_name,
        'customer_email' => $customer_email,
        'total_amount' => $total_amount,
        'payment_method' => $payment_method
    ];

    header("Location: order_success.php");
    exit;
}

// ===============================
// TAMPILAN PAYMENT
// ===============================

if (
    $event_id <= 0 ||
    empty($ticket_quantities)
) {
    header("Location: index.php");
    exit;
}

$eventModel = new Event($db);

$event = $eventModel->find_by_id($event_id);

if (!$event) {
    header("Location: index.php");
    exit;
}

// Ambil ticket
$selected_tickets = [];

foreach ($ticket_quantities as $ticket_id => $quantity) {

    $ticket_id = (int) $ticket_id;
    $quantity = (int) $quantity;

    if ($ticket_id > 0 && $quantity > 0) {
        $selected_tickets[$ticket_id] = $quantity;
    }
}

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

$tickets = $result->data;

$total_quantity = 0;
$total_amount = 0;

foreach ($tickets as $ticket) {

    $quantity = $selected_tickets[$ticket['id']] ?? 0;

    $total_quantity += $quantity;
    $total_amount += $ticket['price'] * $quantity;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Pembayaran - <?= htmlspecialchars($event['name']) ?>
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">

    <div class="container">

        <a
            class="navbar-brand fw-bold"
            href="index.php"
        >
            Event Ticketing System
        </a>

    </div>

</nav>


<div class="container py-5">

    <!-- STEPPER -->

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <div class="row text-center">

                <div class="col-3">

                    <div class="text-muted">
                        1. Pilih Kategori
                    </div>

                </div>

                <div class="col-3">

                    <div class="text-muted">
                        2. Detail Pesanan
                    </div>

                </div>

                <div class="col-3">

                    <div class="fw-bold text-primary">
                        3. Metode Pembayaran
                    </div>

                </div>

                <div class="col-3">

                    <div class="text-muted">
                        4. Selesai
                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="row g-4">

        <!-- PAYMENT METHOD -->

        <div class="col-lg-7">

            <div class="card shadow-sm">

                <div class="card-body p-4">

                    <h3 class="fw-bold mb-2">
                        Metode Pembayaran
                    </h3>

                    <p class="text-muted mb-4">
                        Pilih metode pembayaran yang ingin digunakan.
                    </p>


                    <form method="POST">

                        <input
                            type="hidden"
                            name="event_id"
                            value="<?= $event_id ?>"
                        >


                        <?php foreach ($selected_tickets as $ticket_id => $quantity): ?>

                            <input
                                type="hidden"
                                name="tickets[<?= $ticket_id ?>]"
                                value="<?= $quantity ?>"
                            >

                        <?php endforeach; ?>


                        <input
                            type="hidden"
                            name="customer_name"
                            value="<?= htmlspecialchars($customer_name) ?>"
                        >

                        <input
                            type="hidden"
                            name="customer_email"
                            value="<?= htmlspecialchars($customer_email) ?>"
                        >

                        <input
                            type="hidden"
                            name="customer_phone"
                            value="<?= htmlspecialchars($customer_phone) ?>"
                        >


                        <!-- TRANSFER BANK -->

                        <div class="form-check border rounded p-3 mb-3">

                            <input
                                class="form-check-input"
                                type="radio"
                                name="payment_method"
                                id="transfer_bank"
                                value="transfer_bank"
                                required
                            >

                            <label
                                class="form-check-label w-100"
                                for="transfer_bank"
                            >

                                <strong>
                                    Transfer Bank
                                </strong>

                                <br>

                                <small class="text-muted">
                                    Pembayaran melalui transfer bank
                                </small>

                            </label>

                        </div>


                        <!-- E WALLET -->

                        <div class="form-check border rounded p-3 mb-3">

                            <input
                                class="form-check-input"
                                type="radio"
                                name="payment_method"
                                id="e_wallet"
                                value="e_wallet"
                            >

                            <label
                                class="form-check-label w-100"
                                for="e_wallet"
                            >

                                <strong>
                                    E-Wallet
                                </strong>

                                <br>

                                <small class="text-muted">
                                    GoPay, OVO, DANA, dan lainnya
                                </small>

                            </label>

                        </div>


                        <!-- QRIS -->

                        <div class="form-check border rounded p-3 mb-4">

                            <input
                                class="form-check-input"
                                type="radio"
                                name="payment_method"
                                id="qris"
                                value="qris"
                            >

                            <label
                                class="form-check-label w-100"
                                for="qris"
                            >

                                <strong>
                                    QRIS
                                </strong>

                                <br>

                                <small class="text-muted">
                                    Pembayaran menggunakan QRIS
                                </small>

                            </label>

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary btn-lg w-100"
                        >
                            Bayar Sekarang
                        </button>

                    </form>

                </div>

            </div>

        </div>


        <!-- SUMMARY -->

        <div class="col-lg-5">

            <div class="card shadow-sm">

                <div class="card-body p-4">

                    <h5 class="fw-bold mb-1">
                        Ringkasan Pesanan
                    </h5>

                    <p class="text-muted mb-4">
                        <?= htmlspecialchars($event['name']) ?>
                    </p>


                    <?php foreach ($tickets as $ticket): ?>

                        <?php

                        $quantity =
                            $selected_tickets[$ticket['id']];

                        $subtotal =
                            $ticket['price'] * $quantity;

                        ?>

                        <div
                            class="d-flex justify-content-between mb-3"
                        >

                            <div>

                                <div class="fw-semibold">

                                    <?= htmlspecialchars(
                                        $ticket['name']
                                    ) ?>

                                </div>

                                <small class="text-muted">

                                    <?= $quantity ?> ×

                                    Rp<?= number_format(
                                        $ticket['price'],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>

                                </small>

                            </div>


                            <div class="fw-semibold">

                                Rp<?= number_format(
                                    $subtotal,
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </div>

                        </div>

                    <?php endforeach; ?>


                    <hr>


                    <div
                        class="d-flex justify-content-between"
                    >

                        <span class="text-muted">
                            Total Tiket
                        </span>

                        <span class="fw-semibold">
                            <?= $total_quantity ?> tiket
                        </span>

                    </div>


                    <div
                        class="d-flex justify-content-between mt-2"
                    >

                        <span class="fw-bold">
                            Total Pembayaran
                        </span>

                        <span class="fw-bold text-primary fs-5">

                            Rp<?= number_format(
                                $total_amount,
                                0,
                                ',',
                                '.'
                            ) ?>

                        </span>

                    </div>

                </div>

            </div>


            <!-- CUSTOMER -->

            <div class="card shadow-sm mt-4">

                <div class="card-body">

                    <h6 class="fw-bold">
                        Data Pemesan
                    </h6>

                    <p class="mb-1">
                        <?= htmlspecialchars($customer_name) ?>
                    </p>

                    <p class="mb-1 text-muted">
                        <?= htmlspecialchars($customer_email) ?>
                    </p>

                    <p class="mb-0 text-muted">
                        <?= htmlspecialchars($customer_phone) ?>
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>