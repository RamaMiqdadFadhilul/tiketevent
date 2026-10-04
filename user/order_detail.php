<?php

session_start();

require_once "../bootstrap.php";

$db = new DBconnection();

$eventModel = new Event($db);

$event_id = (int) (
    $_SESSION['selected_event_id']
    ?? $_GET['event_id']
    ?? $_POST['event_id']
    ?? 0
);

$selected_tickets = $_SESSION['selected_tickets'] ?? [];

if ($event_id <= 0 || empty($selected_tickets)) {

    header("Location: index.php");
    exit;
}

$event = $eventModel->find_by_id($event_id);

if (!$event) {

    unset($_SESSION['selected_event_id']);
    unset($_SESSION['selected_tickets']);

    header("Location: index.php");
    exit;
}

$category = null;

if (!empty($event['category_id'])) {

    $categoryResult = $db->send_query(
        "SELECT *
         FROM categories
         WHERE id = $1",
        [
            $event['category_id']
        ]
    );

    $category = $categoryResult->data[0] ?? null;
}


$ticket_ids = array_keys($selected_tickets);

$placeholders = [];
$params = [];

foreach ($ticket_ids as $index => $ticket_id) {

    $placeholder = '$' . ($index + 1);

    $placeholders[] = $placeholder;

    $params[] = (int) $ticket_id;
}

$result = $db->send_query(
    "SELECT *
     FROM tickets
     WHERE event_id = " . $event_id . "
     AND id IN (" . implode(',', $placeholders) . ")
     ORDER BY price",
    $params
);

$tickets = $result->data;

$valid_tickets = [];

$total_amount = 0;


foreach ($tickets as $ticket) {

    $ticket_id = (int) $ticket['id'];

    if (!isset($selected_tickets[$ticket_id])) {
        continue;
    }

    $quantity = (int) $selected_tickets[$ticket_id];

    if ($quantity <= 0) {
        continue;
    }


    if ($quantity > (int) $ticket['stock']) {
        $quantity = (int) $ticket['stock'];
    }

    if ($quantity <= 0) {
        continue;
    }

    $valid_tickets[$ticket_id] = $quantity;

    $subtotal =
        (float) $ticket['price'] *
        $quantity;

    $total_amount += $subtotal;
}

if (empty($valid_tickets)) {
    unset($_SESSION['selected_tickets']);
    unset($_SESSION['selected_event_id']);
    header(
        "Location: select_ticket.php?event_id=" . $event_id
    );
    exit;
}

$_SESSION['selected_tickets'] = $valid_tickets;

$_SESSION['selected_event_id'] = $event_id;

$activePage = 'event';

require_once "templates/navbar.php";

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>Detail Pesanan - <?= Konfigurasi::APP_NAME ?></title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="mb-4">
        <a
            href="select_ticket.php?event_id=<?= $event_id ?>"
            class="text-decoration-none"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Kembali Pilih Tiket
        </a>
        <h2 class="fw-bold mt-3">Detail Pesanan</h2>
        <p class="text-muted mb-0">Lengkapi data pemesan sebelum melanjutkan pembayaran.</p>
    </div>
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4">
                        <?= htmlspecialchars($event['name']) ?>
                    </h4>
                    <div class="mb-4">
                        <div class="d-flex mb-2">
                            <i class="bi bi-geo-alt text-primary me-2"></i>
                            <span>
                                <?= htmlspecialchars(
                                    $event['location']
                                ) ?>
                            </span>
                        </div>
                        <div class="d-flex mb-2">
                            <i class="bi bi-calendar3 text-primary me-2"></i>
                            <span>
                                <?= date(
                                    'd-m-Y',
                                    strtotime($event['event_date'])
                                ) ?>
                            </span>
                        </div>
                        <div class="d-flex mb-2">
                            <i class="bi bi-clock text-primary me-2"></i>
                            <span>
                                <?= date(
                                    'H:i',
                                    strtotime($event['event_time'])
                                ) ?>
                                WIB
                            </span>
                        </div>
                        <?php if ($category): ?>
                            <div class="d-flex">
                                <i class="bi bi-tag text-primary me-2"></i>
                                <span>
                                    <?= htmlspecialchars(
                                        $category['name']
                                    ) ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <hr>
                    <h5 class="fw-bold mb-3">Tiket yang Dipilih</h5>
                    <?php foreach ($tickets as $ticket): ?>
                        <?php
                        $ticket_id = (int) $ticket['id'];
                        if (!isset($valid_tickets[$ticket_id])) {
                            continue;
                        }
                        $quantity =
                            $valid_tickets[$ticket_id];
                        $subtotal =
                            (float) $ticket['price'] *
                            $quantity;
                        ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="fw-semibold">
                                    <?= htmlspecialchars(
                                        $ticket['name']
                                    ) ?>
                                </div>
                                <small class="text-muted">
                                    <?= $quantity ?> x
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
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Total</span>
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
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4">Data Pemesan</h4>
                    <form
                        action="payment.php"
                        method="POST"
                    >
                        <input
                            type="hidden"
                            name="event_id"
                            value="<?= $event_id ?>"
                        >
                        <?php foreach (
                            $valid_tickets as $ticket_id => $quantity
                        ): ?>
                            <input
                                type="hidden"
                                name="tickets[<?= $ticket_id ?>]"
                                value="<?= $quantity ?>"
                            >
                        <?php endforeach; ?>
                        <div class="mb-3">
                            <label
                                for="customer_name"
                                class="form-label"
                            >
                                Nama Lengkap
                            </label>
                            <input
                                type="text"
                                name="customer_name"
                                id="customer_name"
                                class="form-control"
                                placeholder="Masukkan nama lengkap"
                                required
                            >
                        </div>
                        <div class="mb-3">
                            <label
                                for="customer_email"
                                class="form-label"
                            >
                                Email
                            </label>
                            <input
                                type="email"
                                name="customer_email"
                                id="customer_email"
                                class="form-control"
                                placeholder="Masukkan email"
                                required
                            >
                        </div>
                        <div class="mb-4">
                            <label
                                for="customer_phone"
                                class="form-label"
                            >
                                Nomor HP
                            </label>
                            <input
                                type="text"
                                name="customer_phone"
                                id="customer_phone"
                                class="form-control"
                                placeholder="Masukkan nomor HP"
                                required
                            >
                        </div>
                        <div class="bg-light rounded p-3 mb-4">
                            <div class="d-flex justify-content-between">
                                <span>Total Pembayaran</span>
                                <strong class="text-primary">
                                    Rp<?= number_format(
                                        $total_amount,
                                        0,
                                        ',',
                                        '.'
                                    ) ?>
                                </strong>
                            </div>
                        </div>
                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Lanjut ke Pembayaran
                            <i
                                class="bi bi-arrow-right ms-1"
                            ></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>