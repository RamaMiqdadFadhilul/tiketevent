<?php

session_start();

require_once "../bootstrap.php";

$db = new DBconnection();
$eventModel = new Event($db);

$event_id = (int) ($_GET['event_id'] ?? 0);

if ($event_id <= 0) {
    header("Location: index.php");
    exit;
}

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
| Ambil Tiket Event
|--------------------------------------------------------------------------
*/

$result = $db->send_query(
    "SELECT *
     FROM tickets
     WHERE event_id = :event_id
     ORDER BY price",
    [
        'event_id' => $event_id
    ]
);

$tickets = $result->data;


/*
|--------------------------------------------------------------------------
| Proses Pilihan Tiket
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $selected_tickets = [];

    foreach ($_POST['tickets'] ?? [] as $ticket_id => $quantity) {

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
    | Simpan Data Checkout ke Session
    |--------------------------------------------------------------------------
    */

    $_SESSION['checkout'] = [

        'event_id' => $event_id,

        'tickets' => $selected_tickets

    ];


    header("Location: order_detail.php");

    exit;
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
        Pilih Tiket - <?= htmlspecialchars($event['name']) ?>
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


    <!-- BREADCRUMB -->

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

            <li
                class="breadcrumb-item active"
                aria-current="page"
            >
                Pilih Tiket
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

                    <div class="fw-bold text-primary">

                        <div
                            class="mx-auto mb-1 rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;"
                        >
                            1
                        </div>

                        <small>
                            Pilih Tiket
                        </small>

                    </div>

                </div>


                <!-- STEP 2 -->

                <div class="col-3">

                    <div class="text-muted">

                        <div
                            class="mx-auto mb-1 rounded-circle bg-light border d-flex align-items-center justify-content-center"
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
         EVENT HEADER
    ====================================================== -->

    <div class="mb-4">

        <a
            href="event_detail.php?id=<?= $event_id ?>"
            class="btn btn-outline-secondary mb-3"
        >

            <i class="bi bi-arrow-left me-1"></i>

            Kembali ke Detail Event

        </a>


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

            Pilih Tiket

        </h1>


        <p class="text-muted mb-0">

            <?= htmlspecialchars($event['name']) ?>

        </p>

    </div>


    <!-- =====================================================
         TICKET LIST
    ====================================================== -->

    <?php if (empty($tickets)): ?>

        <div class="card border-0 shadow-sm">

            <div class="card-body text-center py-5">

                <i class="bi bi-ticket-perforated display-4 text-muted"></i>

                <h5 class="fw-bold mt-3">

                    Belum ada tiket

                </h5>

                <p class="text-muted mb-3">

                    Saat ini belum tersedia tiket untuk event ini.

                </p>

                <a
                    href="event_detail.php?id=<?= $event_id ?>"
                    class="btn btn-primary"
                >

                    <i class="bi bi-arrow-left me-1"></i>

                    Kembali ke Event

                </a>

            </div>

        </div>

    <?php else: ?>


        <form
            action="order_detail.php"
            method="POST"
        >

            <input
                type="hidden"
                name="event_id"
                value="<?= $event_id ?>"
            >


            <div class="row g-4">

                <?php foreach ($tickets as $ticket): ?>

                    <div class="col-12 col-md-6">

                        <div class="card border-0 shadow-sm h-100">

                            <div class="card-body p-4">


                                <!-- TICKET NAME -->

                                <div class="d-flex justify-content-between align-items-start gap-3">

                                    <div>

                                        <h5 class="fw-bold mb-1">

                                            <?= htmlspecialchars(
                                                $ticket['name']
                                            ) ?>

                                        </h5>

                                        <small class="text-muted">

                                            Tiket event

                                        </small>

                                    </div>


                                    <i
                                        class="bi bi-ticket-perforated text-primary fs-3"
                                    ></i>

                                </div>


                                <!-- PRICE -->

                                <div class="mt-3">

                                    <small class="text-muted">
                                        Harga
                                    </small>

                                    <h4 class="fw-bold text-primary mb-0">

                                        Rp <?= number_format(
                                            $ticket['price'],
                                            0,
                                            ',',
                                            '.'
                                        ) ?>

                                    </h4>

                                </div>


                                <hr>


                                <?php if ($ticket['stock'] > 0): ?>


                                    <!-- STOCK + QUANTITY -->

                                    <div class="d-flex justify-content-between align-items-center">

                                        <div>

                                            <small class="text-muted">
                                                Stok tersedia
                                            </small>

                                            <div class="fw-bold">

                                                <?= $ticket['stock'] ?>

                                                tiket

                                            </div>

                                        </div>


                                        <!-- QUANTITY -->

                                        <div
                                            class="input-group"
                                            style="width: 150px;"
                                        >

                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary btn-minus"
                                                data-ticket-id="<?= $ticket['id'] ?>"
                                            >

                                                <i class="bi bi-dash"></i>

                                            </button>


                                            <input
                                                type="number"
                                                name="tickets[<?= $ticket['id'] ?>]"
                                                class="form-control text-center quantity"
                                                value="0"
                                                min="0"
                                                max="<?= $ticket['stock'] ?>"
                                                data-ticket-id="<?= $ticket['id'] ?>"
                                                data-price="<?= $ticket['price'] ?>"
                                                readonly
                                            >


                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary btn-plus"
                                                data-ticket-id="<?= $ticket['id'] ?>"
                                            >

                                                <i class="bi bi-plus"></i>

                                            </button>

                                        </div>

                                    </div>


                                <?php else: ?>


                                    <!-- SOLD OUT -->

                                    <div class="d-flex justify-content-between align-items-center">

                                        <span class="badge bg-danger-subtle text-danger">

                                            Tiket Habis

                                        </span>

                                    </div>


                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>


            <!-- =================================================
                 ORDER SUMMARY
            ================================================== -->

            <div class="card border-0 shadow-sm mt-4">

                <div class="card-body p-4">

                    <div class="row align-items-center">


                        <div class="col-md-6">

                            <small class="text-muted">
                                Ringkasan Pesanan
                            </small>

                            <h5 class="fw-bold mb-1">
                                Total Tiket
                            </h5>

                            <span
                                class="text-muted"
                                id="total-item"
                            >
                                0 tiket
                            </span>

                        </div>


                        <div class="col-md-6 text-md-end mt-3 mt-md-0">

                            <small class="text-muted">
                                Total Pembayaran
                            </small>

                            <h3
                                class="fw-bold text-primary mb-0"
                                id="total-price"
                            >
                                Rp0
                            </h3>

                        </div>

                    </div>


                    <button
                        type="submit"
                        id="continue-button"
                        class="btn btn-primary btn-lg w-100 mt-4"
                        disabled
                    >

                        Lanjut ke Detail Pesanan

                        <i class="bi bi-arrow-right ms-1"></i>

                    </button>

                </div>

            </div>

        </form>

    <?php endif; ?>

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


<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script>

const quantities =
    document.querySelectorAll('.quantity');

const totalPrice =
    document.getElementById('total-price');

const totalItem =
    document.getElementById('total-item');

const continueButton =
    document.getElementById('continue-button');


function updateTotal() {

    let total = 0;

    let item = 0;


    quantities.forEach(function(input) {

        const quantity =
            parseInt(input.value) || 0;

        const price =
            parseFloat(input.dataset.price) || 0;


        total += quantity * price;

        item += quantity;

    });


    totalPrice.textContent =
        'Rp' + total.toLocaleString('id-ID');


    totalItem.textContent =
        item + ' tiket';


    continueButton.disabled =
        item === 0;

}


/*
|--------------------------------------------------------------------------
| Tombol +
|--------------------------------------------------------------------------
*/

document
    .querySelectorAll('.btn-plus')
    .forEach(function(button) {

        button.addEventListener(
            'click',
            function() {

                const ticketId =
                    this.dataset.ticketId;


                const input =
                    document.querySelector(
                        `.quantity[data-ticket-id="${ticketId}"]`
                    );


                const max =
                    parseInt(input.max);


                let value =
                    parseInt(input.value) || 0;


                if (value < max) {

                    input.value =
                        value + 1;

                }


                updateTotal();

            }
        );

    });


/*
|--------------------------------------------------------------------------
| Tombol -
|--------------------------------------------------------------------------
*/

document
    .querySelectorAll('.btn-minus')
    .forEach(function(button) {

        button.addEventListener(
            'click',
            function() {

                const ticketId =
                    this.dataset.ticketId;


                const input =
                    document.querySelector(
                        `.quantity[data-ticket-id="${ticketId}"]`
                    );


                let value =
                    parseInt(input.value) || 0;


                if (value > 0) {

                    input.value =
                        value - 1;

                }


                updateTotal();

            }
        );

    });


updateTotal();

</script>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>