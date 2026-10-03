<?php

require_once "../bootstrap.php";

$db = new DBconnection();
$eventModel = new Event($db);

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$event = $eventModel->find_by_id($id);

if (!$event) {
    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil kategori event
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
| Ambil harga tiket termurah yang masih tersedia
|--------------------------------------------------------------------------
*/
$result = $db->send_query(
    "SELECT MIN(price) AS min_price
     FROM tickets
     WHERE event_id = :event_id
     AND stock > 0",
    [
        'event_id' => $id
    ]
);

$min_price = $result->data[0]['min_price'] ?? null;

/*
|--------------------------------------------------------------------------
| Status Event
|--------------------------------------------------------------------------
*/
$statusLabel = 'Tidak Diketahui';
$statusClass = 'bg-secondary';

switch ($event['status']) {

    case 'upcoming':
        $statusLabel = 'Akan Datang';
        $statusClass = 'bg-primary';
        break;

    case 'ongoing':
        $statusLabel = 'Sedang Berlangsung';
        $statusClass = 'bg-success';
        break;

    case 'completed':
        $statusLabel = 'Selesai';
        $statusClass = 'bg-secondary';
        break;

    case 'cancelled':
        $statusLabel = 'Dibatalkan';
        $statusClass = 'bg-danger';
        break;
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
        <?= htmlspecialchars($event['name']) ?> - EventTicket
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


<!-- NAVBAR -->

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


<!-- CONTENT -->

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
                    href="index.php#event"
                    class="text-decoration-none"
                >
                    Event
                </a>

            </li>

            <li
                class="breadcrumb-item active"
                aria-current="page"
            >
                <?= htmlspecialchars($event['name']) ?>
            </li>

        </ol>

    </nav>


    <!-- DETAIL EVENT -->

    <div class="card border-0 shadow-sm overflow-hidden mb-4">

        <div class="row g-0">


            <!-- IMAGE -->

            <div class="col-md-5">

                <?php if (!empty($event['image'])): ?>

                    <img
                        src="<?= htmlspecialchars($event['image']) ?>"
                        class="img-fluid w-100 h-100"
                        alt="<?= htmlspecialchars($event['name']) ?>"
                        style="object-fit: cover; min-height: 420px;"
                    >

                <?php else: ?>

                    <div
                        class="bg-secondary-subtle d-flex align-items-center justify-content-center h-100"
                        style="min-height: 420px;"
                    >

                        <div class="text-center text-muted">

                            <i class="bi bi-image display-3"></i>

                            <p class="mt-2 mb-0">
                                Tidak ada gambar
                            </p>

                        </div>

                    </div>

                <?php endif; ?>

            </div>


            <!-- INFORMATION -->

            <div class="col-md-7">

                <div class="card-body p-4 p-lg-5">


                    <!-- CATEGORY -->

                    <?php if ($category): ?>

                        <span class="badge bg-primary-subtle text-primary mb-2">

                            <i class="bi bi-grid me-1"></i>

                            <?= htmlspecialchars($category['name']) ?>

                        </span>

                    <?php endif; ?>


                    <!-- STATUS -->

                    <div class="mb-3">

                        <span class="badge <?= $statusClass ?>">

                            <?= $statusLabel ?>

                        </span>

                    </div>


                    <!-- TITLE -->

                    <h1 class="fw-bold mb-4">

                        <?= htmlspecialchars($event['name']) ?>

                    </h1>


                    <!-- DATE -->

                    <div class="d-flex mb-3">

                        <div class="text-primary me-3">

                            <i class="bi bi-calendar3 fs-5"></i>

                        </div>

                        <div>

                            <small class="text-muted">
                                Tanggal
                            </small>

                            <div class="fw-semibold">

                                <?= date(
                                    'd M Y',
                                    strtotime($event['event_date'])
                                ) ?>

                            </div>

                        </div>

                    </div>


                    <!-- TIME -->

                    <div class="d-flex mb-3">

                        <div class="text-primary me-3">

                            <i class="bi bi-clock fs-5"></i>

                        </div>

                        <div>

                            <small class="text-muted">
                                Waktu
                            </small>

                            <div class="fw-semibold">

                                <?= date(
                                    'H:i',
                                    strtotime($event['event_time'])
                                ) ?>

                                WIB

                            </div>

                        </div>

                    </div>


                    <!-- LOCATION -->

                    <div class="d-flex mb-4">

                        <div class="text-primary me-3">

                            <i class="bi bi-geo-alt fs-5"></i>

                        </div>

                        <div>

                            <small class="text-muted">
                                Lokasi
                            </small>

                            <div class="fw-semibold">

                                <?= htmlspecialchars($event['location']) ?>

                            </div>

                        </div>

                    </div>


                    <hr>


                    <!-- DESCRIPTION -->

                    <h5 class="fw-bold mt-4 mb-3">

                        Tentang Event

                    </h5>

                    <p class="text-muted mb-0">

                        <?= nl2br(
                            htmlspecialchars(
                                $event['description']
                                ?? 'Tidak ada deskripsi.'
                            )
                        ) ?>

                    </p>

                </div>

            </div>

        </div>

    </div>


    <!-- TICKET -->

    <div class="card border-0 shadow-sm">

        <div class="card-body p-4">

            <div class="row align-items-center">


                <!-- PRICE -->

                <div class="col-md-7">

                    <small class="text-muted">
                        Harga tiket mulai dari
                    </small>

                    <?php if ($min_price !== null): ?>

                        <h2 class="fw-bold text-primary mb-0">

                            Rp <?= number_format(
                                $min_price,
                                0,
                                ',',
                                '.'
                            ) ?>

                        </h2>

                    <?php else: ?>

                        <h5 class="text-danger mb-0 mt-1">

                            Tiket tidak tersedia

                        </h5>

                    <?php endif; ?>

                </div>


                <!-- BUTTON -->

                <div class="col-md-5 text-md-end mt-3 mt-md-0">

                    <?php if (
                        $min_price !== null &&
                        in_array($event['status'], ['upcoming', 'ongoing'])
                    ): ?>

                        <a
                            href="select_ticket.php?event_id=<?= $event['id'] ?>"
                            class="btn btn-primary btn-lg px-4"
                        >

                            <i class="bi bi-ticket-perforated me-1"></i>

                            Pesan Tiket

                        </a>

                    <?php elseif ($event['status'] === 'completed'): ?>

                        <button
                            class="btn btn-secondary btn-lg"
                            disabled
                        >

                            Event Selesai

                        </button>

                    <?php elseif ($event['status'] === 'cancelled'): ?>

                        <button
                            class="btn btn-danger btn-lg"
                            disabled
                        >

                            Event Dibatalkan

                        </button>

                    <?php else: ?>

                        <button
                            class="btn btn-secondary btn-lg"
                            disabled
                        >

                            Tiket Habis

                        </button>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- FOOTER -->

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