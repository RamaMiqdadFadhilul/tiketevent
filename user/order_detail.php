<?php

session_start();

require_once "../bootstrap.php";

$db = new DBconnection();

$eventModel = new Event($db);

$event_id = (int) ($_POST['event_id'] ?? 0);
$ticket_quantities = $_POST['tickets'] ?? [];


/*
|--------------------------------------------------------------------------
| Validasi Input
|--------------------------------------------------------------------------
*/

if ($event_id <= 0 || empty($ticket_quantities)) {

    header("Location: index.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| Ambil Event
|--------------------------------------------------------------------------
*/

$event = $eventModel->find_by_id($event_id);

if (!$event) {

    header("Location: index.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| Ambil Kategori Event
|--------------------------------------------------------------------------
*/

$categoryResult = $db->send_query(
    "SELECT name
     FROM categories
     WHERE id = :category_id",
    [
        'category_id' => $event['category_id']
    ]
);

$category = $categoryResult->data[0] ?? null;


/*
|--------------------------------------------------------------------------
| Filter Tiket yang Dipilih
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| Ambil Data Tiket
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| Hitung Total
|--------------------------------------------------------------------------
*/

$total_quantity = 0;
$total_amount = 0;

foreach ($tickets as $ticket) {

    $quantity =
        $selected_tickets[$ticket['id']] ?? 0;

    $total_quantity += $quantity;

    $total_amount +=
        $ticket['price'] * $quantity;
}


/*
|--------------------------------------------------------------------------
| Simpan Pilihan ke Session
|--------------------------------------------------------------------------
*/

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

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

</head>

<body class="bg-light">


<!-- =========================================================
     NAVBAR
========================================================= -->

<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">

    <div class="container">

        <a
            href="index.php"
            class="navbar-brand fw-bold text-primary fs-4"
        >

            <i class="bi bi-ticket-perforated-fill me-1"></i>

            EventTicket

        </a>


        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarMenu"
        >

            <span class="navbar-toggler-icon"></span>

        </button>


        <div
            class="collapse navbar-collapse"
            id="navbarMenu"
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
                        class="nav-link active"
                        href="index.php#event"
                    >
                        Event
                    </a>

                </li>

            </ul>

        </div>

    </div>

</nav>


<!-- =========================================================
     CONTENT
========================================================= -->

<div class="container py-5">


    <!-- =====================================================
         BREADCRUMB
    ====================================================== -->

    <nav aria-label="breadcrumb">

        <ol class="breadcrumb">

            <li class="breadcrumb-item">

                <a
                    href="index.php"
                    class="text-decoration-none"
                >
                    Beranda
                </a>

            </li>

            <li class="breadcrumb-item">

                <a
                    href="event_detail.php?id=<?= $event_id ?>"
                    class="text-decoration-none"
                >
                    <?= htmlspecialchars($event['name']) ?>
                </a>

            </li>

            <li class="breadcrumb-item">

                <a
                    href="select_ticket.php?event_id=<?= $event_id ?>"
                    class="text-decoration-none"
                >
                    Pilih Tiket
                </a>

            </li>

            <li
                class="breadcrumb-item active"
                aria-current="page"
            >
                Detail Pesanan
            </li>

        </ol>

    </nav>


    <!-- =====================================================
         STEPPER
    ====================================================== -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body p-3">

            <div class="row text-center g-0">


                <!-- STEP 1 -->

                <div class="col-3">

                    <div class="text-success">

                        <div
                            class="mx-auto mb-1 rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;"
                        >

                            <i class="bi bi-check"></i>

                        </div>

                        <small>
                            Pilih Tiket
                        </small>

                    </div>

                </div>


                <!-- STEP 2 -->

                <div class="col-3">

                    <div class="fw-bold text-primary">

                        <div
                            class="mx-auto mb-1 rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;"
                        >
                            2
                        </div>

                        <small>
                            Detail Pesanan
                        </small>

                    </div>

                </div>


                <!-- STEP 3 -->

                <div class="col-3">

                    <div class="text-muted">

                        <div
                            class="mx-auto mb-1 rounded-circle bg-light border d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;"
                        >
                            3
                        </div>

                        <small>
                            Pembayaran
                        </small>

                    </div>

                </div>


                <!-- STEP 4 -->

                <div class="col-3">

                    <div class="text-muted">

                        <div
                            class="mx-auto mb-1 rounded-circle bg-light border d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;"
                        >
                            4
                        </div>

                        <small>
                            Selesai
                        </small>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
         EVENT INFO
    ====================================================== -->

    <div class="mb-4">

        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">

            <?php if ($category): ?>

                <span class="badge bg-primary-subtle text-primary">

                    <i class="bi bi-grid me-1"></i>

                    <?= htmlspecialchars($category['name']) ?>

                </span>

            <?php endif; ?>


            <?php if ($event['status'] === 'ongoing'): ?>

                <span class="badge bg-success">

                    Sedang Berlangsung

                </span>

            <?php else: ?>

                <span class="badge bg-primary">

                    Akan Datang

                </span>

            <?php endif; ?>

        </div>


        <h1 class="fw-bold mb-2">

            Detail Pesanan

        </h1>


        <p class="text-muted mb-0">

            Lengkapi data diri untuk melanjutkan pemesanan tiket
            <strong>
                <?= htmlspecialchars($event['name']) ?>
            </strong>.

        </p>

    </div>


    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <div class="row g-4">


        <!-- =================================================
             FORM BIODATA
        ================================================== -->

        <div class="col-lg-7">

            <div class="card border-0 shadow-sm">

                <div class="card-body p-4 p-lg-5">

                    <div class="d-flex align-items-center mb-4">

                        <div
                            class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 48px; height: 48px;"
                        >

                            <i class="bi bi-person fs-5"></i>

                        </div>

                        <div>

                            <h4 class="fw-bold mb-1">

                                Data Pemesan

                            </h4>

                            <p class="text-muted mb-0">

                                Masukkan data yang dapat dihubungi.

                            </p>

                        </div>

                    </div>


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


                        <!-- TICKET DATA -->

                        <?php foreach ($selected_tickets as $ticket_id => $quantity): ?>

                            <input
                                type="hidden"
                                name="tickets[<?= $ticket_id ?>]"
                                value="<?= $quantity ?>"
                            >

                        <?php endforeach; ?>


                        <!-- NAMA -->

                        <div class="mb-4">

                            <label
                                for="customer_name"
                                class="form-label fw-semibold"
                            >

                                Nama Lengkap

                            </label>

                            <div class="input-group">

                                <span class="input-group-text bg-white">

                                    <i class="bi bi-person text-muted"></i>

                                </span>

                                <input
                                    type="text"
                                    id="customer_name"
                                    name="customer_name"
                                    class="form-control"
                                    placeholder="Masukkan nama lengkap"
                                    required
                                >

                            </div>

                        </div>


                        <!-- EMAIL -->

                        <div class="mb-4">

                            <label
                                for="customer_email"
                                class="form-label fw-semibold"
                            >

                                Email

                            </label>

                            <div class="input-group">

                                <span class="input-group-text bg-white">

                                    <i class="bi bi-envelope text-muted"></i>

                                </span>

                                <input
                                    type="email"
                                    id="customer_email"
                                    name="customer_email"
                                    class="form-control"
                                    placeholder="contoh@email.com"
                                    required
                                >

                            </div>

                            <small class="text-muted">

                                Email digunakan untuk informasi pesanan.

                            </small>

                        </div>


                        <!-- NOMOR HP -->

                        <div class="mb-4">

                            <label
                                for="customer_phone"
                                class="form-label fw-semibold"
                            >

                                Nomor HP

                            </label>

                            <div class="input-group">

                                <span class="input-group-text bg-white">

                                    <i class="bi bi-telephone text-muted"></i>

                                </span>

                                <input
                                    type="text"
                                    id="customer_phone"
                                    name="customer_phone"
                                    class="form-control"
                                    placeholder="08xxxxxxxxxx"
                                    required
                                >

                            </div>

                        </div>


                        <div class="alert alert-light border">

                            <i class="bi bi-info-circle text-primary me-2"></i>

                            Pastikan data yang kamu masukkan sudah benar
                            sebelum melanjutkan ke pembayaran.

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary btn-lg w-100"
                        >

                            Lanjut ke Pembayaran

                            <i class="bi bi-arrow-right ms-1"></i>

                        </button>


                    </form>

                </div>

            </div>

        </div>


        <!-- =================================================
             RINGKASAN PESANAN
        ================================================== -->

        <div class="col-lg-5">

            <div class="card border-0 shadow-sm">

                <div class="card-body p-4">


                    <div class="d-flex align-items-center mb-4">

                        <div
                            class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 45px; height: 45px;"
                        >

                            <i class="bi bi-receipt fs-5"></i>

                        </div>

                        <div>

                            <h5 class="fw-bold mb-0">

                                Ringkasan Pesanan

                            </h5>

                            <small class="text-muted">

                                <?= htmlspecialchars($event['name']) ?>

                            </small>

                        </div>

                    </div>


                    <!-- EVENT INFORMATION -->

                    <div class="bg-light rounded-3 p-3 mb-4">

                        <div class="small text-muted mb-2">

                            <i class="bi bi-calendar3 me-1"></i>

                            <?= date(
                                'd M Y',
                                strtotime($event['event_date'])
                            ) ?>

                        </div>

                        <div class="small text-muted mb-2">

                            <i class="bi bi-clock me-1"></i>

                            <?= date(
                                'H:i',
                                strtotime($event['event_time'])
                            ) ?>

                            WIB

                        </div>

                        <div class="small text-muted">

                            <i class="bi bi-geo-alt me-1"></i>

                            <?= htmlspecialchars($event['location']) ?>

                        </div>

                    </div>


                    <!-- TICKET LIST -->

                    <?php foreach ($tickets as $ticket): ?>

                        <?php

                        $quantity =
                            $selected_tickets[$ticket['id']] ?? 0;

                        $subtotal =
                            $ticket['price'] * $quantity;

                        ?>

                        <div class="d-flex justify-content-between align-items-start mb-3">

                            <div class="pe-3">

                                <div class="fw-semibold">

                                    <?= htmlspecialchars(
                                        $ticket['name']
                                    ) ?>

                                </div>

                                <small class="text-muted">

                                    <?= $quantity ?> ×

                                    Rp <?= number_format(
                                        $ticket['price'],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>

                                </small>

                            </div>


                            <div class="fw-semibold text-nowrap">

                                Rp <?= number_format(
                                    $subtotal,
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </div>

                        </div>

                    <?php endforeach; ?>


                    <hr>


                    <!-- TOTAL TICKET -->

                    <div class="d-flex justify-content-between mb-2">

                        <span class="text-muted">

                            Total Tiket

                        </span>

                        <span class="fw-semibold">

                            <?= $total_quantity ?> tiket

                        </span>

                    </div>


                    <!-- TOTAL -->

                    <div class="d-flex justify-content-between align-items-center">

                        <span class="fw-bold">

                            Total Pembayaran

                        </span>

                        <span class="fw-bold text-primary fs-5">

                            Rp <?= number_format(
                                $total_amount,
                                0,
                                ',',
                                '.'
                            ) ?>

                        </span>

                    </div>


                    <div class="mt-4">

                        <a
                            href="select_ticket.php?event_id=<?= $event_id ?>"
                            class="btn btn-outline-secondary w-100"
                        >

                            <i class="bi bi-arrow-left me-1"></i>

                            Ubah Pilihan Tiket

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- =========================================================
     FOOTER
========================================================= -->

<footer class="bg-dark text-white mt-5">

    <div class="container py-5">

        <div class="row g-4">


            <div class="col-md-6">

                <h5 class="fw-bold">

                    <i class="bi bi-ticket-perforated-fill me-1"></i>

                    EventTicket

                </h5>

                <p class="text-white-50">

                    Platform untuk menemukan event menarik
                    dan membeli tiket dengan mudah.

                </p>

            </div>


            <div class="col-md-3">

                <h6 class="fw-bold">
                    Jelajahi
                </h6>

                <ul class="list-unstyled">

                    <li class="mb-2">

                        <a
                            href="index.php"
                            class="text-white-50 text-decoration-none"
                        >
                            Beranda
                        </a>

                    </li>

                    <li class="mb-2">

                        <a
                            href="index.php#kategori"
                            class="text-white-50 text-decoration-none"
                        >
                            Kategori
                        </a>

                    </li>

                    <li>

                        <a
                            href="index.php#event"
                            class="text-white-50 text-decoration-none"
                        >
                            Event
                        </a>

                    </li>

                </ul>

            </div>


            <div class="col-md-3">

                <h6 class="fw-bold">
                    Bantuan
                </h6>

                <p class="text-white-50 mb-0">

                    Hubungi kami jika membutuhkan bantuan.

                </p>

            </div>

        </div>


        <hr class="border-secondary my-4">


        <small class="text-white-50">

            © 2026 EventTicket. All rights reserved.

        </small>

    </div>

</footer>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>