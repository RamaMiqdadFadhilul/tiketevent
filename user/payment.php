<?php

session_start();

require_once "../bootstrap.php";

$db = new DBconnection();

$event_id = (int) ($_POST['event_id'] ?? 0);
$ticket_quantities = $_POST['tickets'] ?? [];

$customer_name = trim($_POST['customer_name'] ?? '');
$customer_email = trim($_POST['customer_email'] ?? '');
$customer_phone = trim($_POST['customer_phone'] ?? '');

// =====================================================
// VALIDASI DATA
// =====================================================

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

// =====================================================
// AMBIL EVENT
// =====================================================

$eventModel = new Event($db);

$event = $eventModel->find_by_id($event_id);

if (!$event) {
    header("Location: index.php");
    exit;
}

// =====================================================
// AMBIL TIKET YANG DIPILIH
// =====================================================

$selected_tickets = [];

foreach ($ticket_quantities as $ticket_id => $quantity) {

    $ticket_id = (int) $ticket_id;
    $quantity = (int) $quantity;

    if ($ticket_id > 0 && $quantity > 0) {
        $selected_tickets[$ticket_id] = $quantity;
    }
}

if (empty($selected_tickets)) {
    header("Location: select_ticket.php?event_id=" . $event_id);
    exit;
}

// =====================================================
// QUERY TIKET
// =====================================================

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

// =====================================================
// HITUNG TOTAL
// =====================================================

$total_quantity = 0;
$total_amount = 0;

foreach ($tickets as $ticket) {

    $quantity = $selected_tickets[$ticket['id']] ?? 0;

    $total_quantity += $quantity;

    $total_amount += $ticket['price'] * $quantity;
}

// =====================================================
// CATEGORY
// =====================================================

$categoryResult = $db->send_query(
    "SELECT name
     FROM categories
     WHERE id = :id",
    [
        'id' => $event['category_id']
    ]
);

$category_name = $categoryResult->data[0]['name'] ?? 'Event';

// =====================================================
// STATUS
// =====================================================

$status = $event['status'];

$status_label = match ($status) {
    'upcoming' => 'Akan Datang',
    'ongoing' => 'Sedang Berlangsung',
    'completed' => 'Selesai',
    'cancelled' => 'Dibatalkan',
    default => ucfirst($status)
};

$status_class = match ($status) {
    'upcoming' => 'bg-primary',
    'ongoing' => 'bg-success',
    'completed' => 'bg-secondary',
    'cancelled' => 'bg-danger',
    default => 'bg-secondary'
};

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

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

</head>

<body class="bg-light">

<!-- =====================================================
     NAVBAR
===================================================== -->

<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">

    <div class="container">

        <a
            class="navbar-brand fw-bold text-primary"
            href="index.php"
        >
            <i class="bi bi-ticket-perforated-fill me-2"></i>
            EventTicket
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div
            class="collapse navbar-collapse"
            id="navbarNav"
        >

            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="index.php"
                    >
                        Beranda
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="index.php#kategori"
                    >
                        Kategori
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="index.php#event"
                    >
                        Event
                    </a>
                </li>

            </ul>

        </div>

    </div>

</nav>


<!-- =====================================================
     CONTENT
===================================================== -->

<div class="container py-5">

    <!-- BREADCRUMB -->

   <!-- BREADCRUMB -->

    <nav aria-label="breadcrumb" class="mb-4">

        <ol class="breadcrumb mb-0">

            <li class="breadcrumb-item">
                <a
                    href="index.php"
                    class="text-decoration-none text-muted"
                >
                    Beranda
                </a>
            </li>

            <li class="breadcrumb-item">
                Checkout
            </li>

            <li class="breadcrumb-item active">
                Pembayaran
            </li>

        </ol>

    </nav>

    <!-- =================================================
         STEPPER
    ================================================== -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body py-4">

            <div class="row text-center">

                <!-- STEP 1 -->

                <div class="col-3">

                    <div class="d-flex justify-content-center mb-2">

                        <div
                            class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;"
                        >
                            <i class="bi bi-check-lg"></i>
                        </div>

                    </div>

                    <small class="text-success fw-semibold">
                        Pilih Tiket
                    </small>

                </div>


                <!-- STEP 2 -->

                <div class="col-3">

                    <div class="d-flex justify-content-center mb-2">

                        <div
                            class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;"
                        >
                            <i class="bi bi-check-lg"></i>
                        </div>

                    </div>

                    <small class="text-success fw-semibold">
                        Detail Pesanan
                    </small>

                </div>


                <!-- STEP 3 -->

                <div class="col-3">

                    <div class="d-flex justify-content-center mb-2">

                        <div
                            class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;"
                        >
                            3
                        </div>

                    </div>

                    <small class="text-primary fw-bold">
                        Pembayaran
                    </small>

                </div>


                <!-- STEP 4 -->

                <div class="col-3">

                    <div class="d-flex justify-content-center mb-2">

                        <div
                            class="rounded-circle bg-light text-secondary border d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;"
                        >
                            4
                        </div>

                    </div>

                    <small class="text-muted">
                        Selesai
                    </small>

                </div>

            </div>

        </div>

    </div>


    <!-- =================================================
         EVENT INFO
    ================================================== -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body p-4">

            <div class="d-flex flex-wrap gap-2 mb-2">

                <span class="badge bg-primary">
                    <?= htmlspecialchars($category_name) ?>
                </span>

                <span class="badge <?= $status_class ?>">
                    <?= $status_label ?>
                </span>

            </div>

            <h3 class="fw-bold mb-3">
                <?= htmlspecialchars($event['name']) ?>
            </h3>

            <div class="row g-3 text-muted">

                <div class="col-md-4">

                    <i class="bi bi-calendar-event me-2 text-primary"></i>

                    <?= date(
                        'd F Y',
                        strtotime($event['event_date'])
                    ) ?>

                </div>

                <div class="col-md-4">

                    <i class="bi bi-clock me-2 text-primary"></i>

                    <?= date(
                        'H:i',
                        strtotime($event['event_time'])
                    ) ?>
                    WIB

                </div>

                <div class="col-md-4">

                    <i class="bi bi-geo-alt me-2 text-primary"></i>

                    <?= htmlspecialchars($event['location']) ?>

                </div>

            </div>

        </div>

    </div>


    <!-- =================================================
         PAYMENT + SUMMARY
    ================================================== -->

    <div class="row g-4">

        <!-- PAYMENT -->

        <div class="col-lg-7">

            <div class="card border-0 shadow-sm">

                <div class="card-body p-4">

                    <h4 class="fw-bold mb-1">
                        Metode Pembayaran
                    </h4>

                    <p class="text-muted mb-4">
                        Pilih metode pembayaran yang ingin digunakan.
                    </p>


                    <form
                        action="process_payment.php"
                        method="POST"
                    >

                        <!-- EVENT -->

                        <input
                            type="hidden"
                            name="event_id"
                            value="<?= $event_id ?>"
                        >


                        <!-- TICKETS -->

                        <?php foreach ($selected_tickets as $ticket_id => $quantity): ?>

                            <input
                                type="hidden"
                                name="tickets[<?= $ticket_id ?>]"
                                value="<?= $quantity ?>"
                            >

                        <?php endforeach; ?>


                        <!-- CUSTOMER -->

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

                        <div class="mb-3">

                            <input
                                type="radio"
                                class="btn-check"
                                name="payment_method"
                                id="transfer_bank"
                                value="transfer_bank"
                                autocomplete="off"
                                required
                            >

                            <label
                                class="btn btn-outline-primary w-100 text-start p-3"
                                for="transfer_bank"
                            >

                                <div class="d-flex align-items-center">

                                    <div
                                        class="bg-primary bg-opacity-10 text-primary rounded p-3 me-3"
                                    >
                                        <i class="bi bi-bank fs-4"></i>
                                    </div>

                                    <div>

                                        <div class="fw-bold">
                                            Transfer Bank
                                        </div>

                                        <small class="text-muted">
                                            Pembayaran melalui transfer bank
                                        </small>

                                    </div>

                                </div>

                            </label>

                        </div>


                        <!-- E-WALLET -->

                        <div class="mb-3">

                            <input
                                type="radio"
                                class="btn-check"
                                name="payment_method"
                                id="e_wallet"
                                value="e_wallet"
                                autocomplete="off"
                            >

                            <label
                                class="btn btn-outline-primary w-100 text-start p-3"
                                for="e_wallet"
                            >

                                <div class="d-flex align-items-center">

                                    <div
                                        class="bg-primary bg-opacity-10 text-primary rounded p-3 me-3"
                                    >
                                        <i class="bi bi-wallet2 fs-4"></i>
                                    </div>

                                    <div>

                                        <div class="fw-bold">
                                            E-Wallet
                                        </div>

                                        <small class="text-muted">
                                            GoPay, OVO, DANA, dan lainnya
                                        </small>

                                    </div>

                                </div>

                            </label>

                        </div>


                        <!-- QRIS -->

                        <div class="mb-4">

                            <input
                                type="radio"
                                class="btn-check"
                                name="payment_method"
                                id="qris"
                                value="qris"
                                autocomplete="off"
                            >

                            <label
                                class="btn btn-outline-primary w-100 text-start p-3"
                                for="qris"
                            >

                                <div class="d-flex align-items-center">

                                    <div
                                        class="bg-primary bg-opacity-10 text-primary rounded p-3 me-3"
                                    >
                                        <i class="bi bi-qr-code fs-4"></i>
                                    </div>

                                    <div>

                                        <div class="fw-bold">
                                            QRIS
                                        </div>

                                        <small class="text-muted">
                                            Pembayaran menggunakan QRIS
                                        </small>

                                    </div>

                                </div>

                            </label>

                        </div>


                        <!-- INFO -->

                        <div class="alert alert-info d-flex align-items-start">

                            <i class="bi bi-info-circle me-2 mt-1"></i>

                            <div>
                                Pembayaran pada sistem ini masih bersifat
                                <strong>simulasi</strong>.
                            </div>

                        </div>


                        <!-- BUTTON -->

                        <button
                            type="submit"
                            class="btn btn-primary btn-lg w-100"
                        >

                            <i class="bi bi-credit-card me-2"></i>

                            Bayar Rp<?= number_format(
                                $total_amount,
                                0,
                                ',',
                                '.'
                            ) ?>

                        </button>

                    </form>

                </div>

            </div>

        </div>


        <!-- =================================================
             SUMMARY
        ================================================== -->

        <div class="col-lg-5">

            <div class="card border-0 shadow-sm">

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
                            $selected_tickets[$ticket['id']] ?? 0;

                        $subtotal =
                            $ticket['price'] * $quantity;

                        ?>

                        <div class="d-flex justify-content-between mb-3">

                            <div>

                                <div class="fw-semibold">
                                    <?= htmlspecialchars($ticket['name']) ?>
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


                    <div class="d-flex justify-content-between mb-2">

                        <span class="text-muted">
                            Total Tiket
                        </span>

                        <span class="fw-semibold">
                            <?= $total_quantity ?> tiket
                        </span>

                    </div>


                    <div class="d-flex justify-content-between">

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


            <!-- DATA PEMESAN -->

            <div class="card border-0 shadow-sm mt-4">

                <div class="card-body p-4">

                    <h6 class="fw-bold mb-3">

                        <i class="bi bi-person me-2"></i>

                        Data Pemesan

                    </h6>

                    <div class="mb-2">

                        <small class="text-muted">
                            Nama
                        </small>

                        <div class="fw-semibold">
                            <?= htmlspecialchars($customer_name) ?>
                        </div>

                    </div>

                    <div class="mb-2">

                        <small class="text-muted">
                            Email
                        </small>

                        <div>
                            <?= htmlspecialchars($customer_email) ?>
                        </div>

                    </div>

                    <div>

                        <small class="text-muted">
                            No. Telepon
                        </small>

                        <div>
                            <?= htmlspecialchars($customer_phone) ?>
                        </div>

                    </div>

                </div>

            </div>


            <!-- BACK -->

            <div class="text-center mt-3">

                <a
                    href="order_detail.php"
                    class="text-decoration-none text-muted"
                >
                    <i class="bi bi-arrow-left me-1"></i>
                    Kembali ke Detail Pesanan
                </a>

            </div>

        </div>

    </div>

</div>


<!-- =====================================================
     FOOTER
===================================================== -->

<footer class="bg-dark text-white mt-5">

    <div class="container py-4">

        <div class="row">

            <div class="col-md-6">

                <h5 class="fw-bold">
                    <i class="bi bi-ticket-perforated-fill me-2"></i>
                    EventTicket
                </h5>

                <p class="text-white-50 mb-0">
                    Platform pemesanan tiket event dengan mudah dan cepat.
                </p>

            </div>

            <div class="col-md-6 text-md-end mt-3 mt-md-0">

                <small class="text-white-50">
                    © <?= date('Y') ?> EventTicket.
                    All rights reserved.
                </small>

            </div>

        </div>

    </div>

</footer>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>