<?php

session_start();

require_once "../bootstrap.php";

$db = new DBconnection();

$eventModel = new Event($db);

$event_id = (int) ($_POST['event_id'] ?? 0);
$ticket_quantities = $_POST['tickets'] ?? [];

if ($event_id <= 0 || empty($ticket_quantities)) {
    header("Location: index.php");
    exit;
}


// =========================
// AMBIL EVENT
// =========================

$event = $eventModel->find_by_id($event_id);

if (!$event) {
    header("Location: index.php");
    exit;
}


// =========================
// FILTER TIKET YANG DIPILIH
// =========================

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


// =========================
// AMBIL DATA TIKET
// =========================

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


// =========================
// HITUNG TOTAL
// =========================

$total_quantity = 0;
$total_amount = 0;

foreach ($tickets as $ticket) {

    $quantity =
        $selected_tickets[$ticket['id']] ?? 0;

    $total_quantity += $quantity;

    $total_amount +=
        $ticket['price'] * $quantity;
}


// =========================
// SIMPAN PILIHAN KE SESSION
// =========================

$_SESSION['checkout'] = [
    'event_id' => $event_id,
    'tickets' => $selected_tickets
];

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
        Detail Pesanan - <?= htmlspecialchars($event['name']) ?>
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body class="bg-light">


<!-- NAVBAR -->

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

                <div class="col-4">

                    <div class="text-muted">
                        1. Pilih Kategori
                    </div>

                </div>


                <div class="col-4">

                    <div class="fw-bold text-primary">
                        2. Detail Pesanan
                    </div>

                </div>


                <div class="col-4">

                    <div class="text-muted">
                        3. Metode Pembayaran
                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="row g-4">


        <!-- FORM BIODATA -->

        <div class="col-lg-7">

            <div class="card shadow-sm">

                <div class="card-body p-4">

                    <h3 class="fw-bold mb-2">
                        Detail Pesanan
                    </h3>

                    <p class="text-muted mb-4">
                        Isi data diri untuk melanjutkan pemesanan tiket.
                    </p>


                    <form
                        action="payment.php"
                        method="POST"
                    >


                        <!-- EVENT ID -->

                        <input
                            type="hidden"
                            name="event_id"
                            value="<?= $event_id ?>"
                        >


                        <!-- TIKET YANG DIPILIH -->

                        <?php foreach ($selected_tickets as $ticket_id => $quantity): ?>

                            <input
                                type="hidden"
                                name="tickets[<?= $ticket_id ?>]"
                                value="<?= $quantity ?>"
                            >

                        <?php endforeach; ?>


                        <!-- NAMA -->

                        <div class="mb-3">

                            <label
                                for="customer_name"
                                class="form-label fw-semibold"
                            >
                                Nama Lengkap
                            </label>

                            <input
                                type="text"
                                id="customer_name"
                                name="customer_name"
                                class="form-control"
                                placeholder="Masukkan nama lengkap"
                                required
                            >

                        </div>


                        <!-- EMAIL -->

                        <div class="mb-3">

                            <label
                                for="customer_email"
                                class="form-label fw-semibold"
                            >
                                Email
                            </label>

                            <input
                                type="email"
                                id="customer_email"
                                name="customer_email"
                                class="form-control"
                                placeholder="contoh@email.com"
                                required
                            >

                        </div>


                        <!-- NOMOR HP -->

                        <div class="mb-4">

                            <label
                                for="customer_phone"
                                class="form-label fw-semibold"
                            >
                                Nomor HP
                            </label>

                            <input
                                type="text"
                                id="customer_phone"
                                name="customer_phone"
                                class="form-control"
                                placeholder="08xxxxxxxxxx"
                                required
                            >

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary btn-lg w-100"
                        >
                            Lanjut ke Pembayaran
                        </button>


                    </form>

                </div>

            </div>

        </div>


        <!-- RINGKASAN PESANAN -->

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

                        <div class="d-flex justify-content-between mb-3">

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


                    <div class="d-flex justify-content-between">

                        <span class="text-muted">
                            Total Tiket
                        </span>

                        <span class="fw-semibold">
                            <?= $total_quantity ?> tiket
                        </span>

                    </div>


                    <div class="d-flex justify-content-between mt-2">

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

        </div>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>