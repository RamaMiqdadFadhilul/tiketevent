<?php

require_once "../bootstrap.php";

$db = new DBconnection();


/*
|--------------------------------------------------------------------------
| REKOMENDASI EVENT
|--------------------------------------------------------------------------
*/

$recommendationResult = $db->send_query(
    "SELECT
        e.*,
        c.name AS category_name,
        MIN(t.price) AS min_price
     FROM events e

     LEFT JOIN categories c
        ON c.id = e.category_id

     LEFT JOIN tickets t
        ON t.event_id = e.id
        AND t.stock > 0

     WHERE e.status IN ('upcoming', 'ongoing')

     GROUP BY
        e.id,
        c.name

     ORDER BY
        e.created_at DESC

     LIMIT 8"
);

$recommendationEvents = $recommendationResult->data;


/*
|--------------------------------------------------------------------------
| EVENT LAINNYA
|--------------------------------------------------------------------------
*/

$eventResult = $db->send_query(
    "SELECT
        e.*,
        c.name AS category_name,
        MIN(t.price) AS min_price

     FROM events e

     LEFT JOIN categories c
        ON c.id = e.category_id

     LEFT JOIN tickets t
        ON t.event_id = e.id
        AND t.stock > 0

     WHERE e.status IN ('upcoming', 'ongoing')

     GROUP BY
        e.id,
        c.name

     ORDER BY
        e.event_date,
        e.event_time

     LIMIT 40"
);

$events = $eventResult->data;


/*
|--------------------------------------------------------------------------
| KATEGORI
|--------------------------------------------------------------------------
*/

$categoryResult = $db->send_query(
    "SELECT *
     FROM categories
     ORDER BY name"
);

$categories = $categoryResult->data;


/*
|--------------------------------------------------------------------------
| FUNCTION
|--------------------------------------------------------------------------
*/

function formatTanggal($date)
{
    if (empty($date)) {
        return '-';
    }

    return date(
        'd M Y',
        strtotime($date)
    );
}


function formatHarga($price)
{
    if ($price === null || $price === '') {
        return 'Tiket belum tersedia';
    }

    return 'Rp ' . number_format(
        $price,
        0,
        ',',
        '.'
    );
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

    <title>EventTicket - Temukan Event Favoritmu</title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Bootstrap Icons -->

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

        <!-- Logo -->

        <a
            href="index.php"
            class="navbar-brand fw-bold text-primary fs-4"
        >

            <i class="bi bi-ticket-perforated-fill me-1"></i>

            EventTicket

        </a>


        <!-- Mobile Toggle -->

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarMenu"
        >

            <span class="navbar-toggler-icon"></span>

        </button>


        <!-- Menu -->

        <div
            class="collapse navbar-collapse"
            id="navbarMenu"
        >

            <ul class="navbar-nav ms-auto">

                <li class="nav-item">

                    <a
                        class="nav-link active"
                        href="index.php"
                    >
                        Beranda
                    </a>

                </li>

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="#kategori"
                    >
                        Kategori
                    </a>

                </li>

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="#event"
                    >
                        Event
                    </a>

                </li>

            </ul>

        </div>

    </div>

</nav>


<!-- =========================================================
     HERO
========================================================= -->

<div class="container mt-4">

    <div
        class="bg-primary rounded-4 p-4 p-md-5 text-white shadow-sm"
    >

        <div class="row align-items-center">

            <div class="col-md-8">

                <span class="badge bg-white text-primary mb-3">
                    Event Ticketing System
                </span>

                <h1 class="fw-bold display-6">
                    Temukan Event Favoritmu
                </h1>

                <p class="lead mb-0">
                    Jelajahi berbagai event menarik dan pesan
                    tiketnya dengan mudah.
                </p>

            </div>


            <div class="col-md-4 d-none d-md-block text-center">

                <i
                    class="bi bi-ticket-perforated"
                    style="font-size: 100px;"
                ></i>

            </div>

        </div>

    </div>

</div>


<!-- =========================================================
     KATEGORI
========================================================= -->

<div
    class="container mt-5"
    id="kategori"
>

    <div class="mb-4">

        <span class="badge bg-primary-subtle text-primary mb-2">
            Jelajahi
        </span>

        <h4 class="fw-bold mb-1">
            Kategori Event
        </h4>

        <p class="text-muted mb-0">
            Temukan event berdasarkan kategori favoritmu.
        </p>

    </div>


    <?php if (!empty($categories)): ?>

        <div class="row g-3">

            <?php foreach ($categories as $category): ?>

                <div class="col-6 col-md-4 col-lg-3">

                    <a
                        href="category.php?id=<?= $category['id'] ?>"
                        class="text-decoration-none"
                    >

                        <div
                            class="card border-0 shadow-sm h-100"
                        >

                            <div class="card-body text-center py-4">

                                <div
                                    class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                    style="width: 55px; height: 55px;"
                                >

                                    <i class="bi bi-grid-3x3-gap fs-4"></i>

                                </div>


                                <h6 class="fw-bold text-dark mb-0">

                                    <?= htmlspecialchars(
                                        $category['name']
                                    ) ?>

                                </h6>

                            </div>

                        </div>

                    </a>

                </div>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="alert alert-light border">

            Belum ada kategori event.

        </div>

    <?php endif; ?>

</div>


<!-- =========================================================
     REKOMENDASI EVENT
========================================================= -->

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-end mb-4">

        <div>

            <span class="badge bg-primary-subtle text-primary mb-2">
                Pilihan Untukmu
            </span>

            <h4 class="fw-bold mb-1">
                Rekomendasi Event
            </h4>

            <p class="text-muted mb-0">
                Event pilihan yang mungkin kamu sukai.
            </p>

        </div>

    </div>


    <?php if (!empty($recommendationEvents)): ?>

        <div class="row g-4">

            <?php foreach ($recommendationEvents as $event): ?>

                <div class="col-12 col-sm-6 col-lg-3">

                    <div
                        class="card h-100 border-0 shadow-sm"
                    >

                        <!-- Image -->

                        <?php if (!empty($event['image'])): ?>

                            <img
                                src="<?= htmlspecialchars($event['image']) ?>"
                                class="card-img-top"
                                alt="<?= htmlspecialchars($event['name']) ?>"
                                style="height: 180px; object-fit: cover;"
                            >

                        <?php else: ?>

                            <div
                                class="bg-secondary-subtle d-flex align-items-center justify-content-center"
                                style="height: 180px;"
                            >

                                <div class="text-center text-muted">

                                    <i class="bi bi-image fs-1"></i>

                                    <div class="small">
                                        Tidak ada gambar
                                    </div>

                                </div>

                            </div>

                        <?php endif; ?>


                        <!-- Card Body -->

                        <div class="card-body d-flex flex-column">

                            <?php if (!empty($event['category_name'])): ?>

                                <span
                                    class="badge bg-primary-subtle text-primary align-self-start mb-2"
                                >

                                    <?= htmlspecialchars(
                                        $event['category_name']
                                    ) ?>

                                </span>

                            <?php endif; ?>


                            <h6 class="fw-bold">

                                <?= htmlspecialchars(
                                    $event['name']
                                ) ?>

                            </h6>


                            <div class="small text-muted mb-1">

                                <i class="bi bi-calendar3 me-1"></i>

                                <?= formatTanggal(
                                    $event['event_date']
                                ) ?>

                            </div>


                            <div class="small text-muted mb-3">

                                <i class="bi bi-geo-alt me-1"></i>

                                <?= htmlspecialchars(
                                    $event['location']
                                ) ?>

                            </div>


                            <div
                                class="d-flex justify-content-between align-items-end mt-auto"
                            >

                                <div>

                                    <small class="text-muted">
                                        Mulai dari
                                    </small>

                                    <div class="fw-bold text-primary">

                                        <?= formatHarga(
                                            $event['min_price']
                                        ) ?>

                                    </div>

                                </div>


                                <a
                                    href="event_detail.php?id=<?= $event['id'] ?>"
                                    class="btn btn-primary btn-sm"
                                >
                                    Beli
                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="alert alert-light border">

            Belum ada rekomendasi event.

        </div>

    <?php endif; ?>

</div>


<!-- =========================================================
     SEMUA EVENT
========================================================= -->

<div
    class="container mt-5 mb-5"
    id="event"
>

    <div class="d-flex justify-content-between align-items-end mb-4">

        <div>

            <span class="badge bg-light text-dark border mb-2">
                Semua Event
            </span>

            <h4 class="fw-bold mb-1">
                Event Menarik Lainnya
            </h4>

            <p class="text-muted mb-0">
                Temukan berbagai event menarik dan pesan tiketnya.
            </p>

        </div>

    </div>


    <?php if (!empty($events)): ?>

        <div class="row g-4">

            <?php foreach ($events as $event): ?>

                <div class="col-12 col-sm-6 col-lg-3">

                    <div
                        class="card h-100 border-0 shadow-sm"
                    >

                        <!-- Event Image -->

                        <?php if (!empty($event['image'])): ?>

                            <img
                                src="<?= htmlspecialchars($event['image']) ?>"
                                class="card-img-top"
                                alt="<?= htmlspecialchars($event['name']) ?>"
                                style="height: 180px; object-fit: cover;"
                            >

                        <?php else: ?>

                            <div
                                class="bg-secondary-subtle d-flex align-items-center justify-content-center"
                                style="height: 180px;"
                            >

                                <div class="text-center text-muted">

                                    <i class="bi bi-image fs-1"></i>

                                    <p class="small mb-0">
                                        Event Belum Memiliki Gambar
                                    </p>

                                </div>

                            </div>

                        <?php endif; ?>


                        <!-- Card Body -->

                        <div class="card-body d-flex flex-column">

                            <?php if (!empty($event['category_name'])): ?>

                                <span
                                    class="badge bg-primary-subtle text-primary align-self-start mb-2"
                                >

                                    <?= htmlspecialchars(
                                        $event['category_name']
                                    ) ?>

                                </span>

                            <?php endif; ?>


                            <h6 class="fw-bold">

                                <?= htmlspecialchars(
                                    $event['name']
                                ) ?>

                            </h6>


                            <div class="small text-muted mb-1">

                                <i class="bi bi-calendar3 me-1"></i>

                                <?= formatTanggal(
                                    $event['event_date']
                                ) ?>

                            </div>


                            <div class="small text-muted mb-1">

                                <i class="bi bi-clock me-1"></i>

                                <?= date(
                                    'H:i',
                                    strtotime($event['event_time'])
                                ) ?>

                                WIB

                            </div>


                            <div class="small text-muted mb-3">

                                <i class="bi bi-geo-alt me-1"></i>

                                <?= htmlspecialchars(
                                    $event['location']
                                ) ?>

                            </div>


                            <div class="mt-auto">

                                <div
                                    class="d-flex justify-content-between align-items-end"
                                >

                                    <div>

                                        <small class="text-muted">
                                            Mulai dari
                                        </small>

                                        <div class="fw-bold text-primary">

                                            <?= formatHarga(
                                                $event['min_price']
                                            ) ?>

                                        </div>

                                    </div>


                                    <a
                                        href="event_detail.php?id=<?= $event['id'] ?>"
                                        class="btn btn-primary btn-sm"
                                    >
                                        Beli Tiket
                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="card border-0 shadow-sm">

            <div class="card-body text-center py-5">

                <i
                    class="bi bi-calendar-x display-5 text-muted"
                ></i>

                <h5 class="mt-3">
                    Belum ada event
                </h5>

                <p class="text-muted mb-0">
                    Saat ini belum ada event yang tersedia.
                </p>

            </div>

        </div>

    <?php endif; ?>

</div>


<!-- =========================================================
     FOOTER
========================================================= -->

<footer class="bg-dark text-white">

    <div class="container py-5">

        <div class="row g-4">

            <!-- Brand -->

            <div class="col-md-5">

                <h5 class="fw-bold">

                    <i class="bi bi-ticket-perforated-fill me-1"></i>

                    EventTicket

                </h5>

                <p class="text-white-50">

                    Platform untuk menemukan event menarik
                    dan membeli tiket dengan mudah.

                </p>

            </div>


            <!-- Jelajahi -->

            <div class="col-6 col-md-3">

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
                            href="#kategori"
                            class="text-white-50 text-decoration-none"
                        >
                            Kategori
                        </a>

                    </li>

                    <li>

                        <a
                            href="#event"
                            class="text-white-50 text-decoration-none"
                        >
                            Event
                        </a>

                    </li>

                </ul>

            </div>


            <!-- Bantuan -->

            <div class="col-6 col-md-2">

                <h6 class="fw-bold">
                    Bantuan
                </h6>

                <ul class="list-unstyled">

                    <li class="mb-2">

                        <span class="text-white-50">
                            Pusat Bantuan
                        </span>

                    </li>

                    <li>

                        <span class="text-white-50">
                            Hubungi Kami
                        </span>

                    </li>

                </ul>

            </div>


            <!-- Sosial Media -->

            <div class="col-md-2">

                <h6 class="fw-bold">
                    Ikuti Kami
                </h6>

                <div class="d-flex gap-3">

                    <i class="bi bi-instagram"></i>

                    <i class="bi bi-facebook"></i>

                    <i class="bi bi-twitter-x"></i>

                </div>

            </div>

        </div>


        <hr class="border-secondary my-4">


        <div class="d-flex justify-content-between flex-wrap gap-2">

            <small class="text-white-50">

                © 2026 EventTicket.
                All rights reserved.

            </small>


            <small class="text-white-50">

                Event Ticketing System

            </small>

        </div>

    </div>

</footer>


<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>