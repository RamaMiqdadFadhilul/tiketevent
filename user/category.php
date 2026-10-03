<?php

require_once "../bootstrap.php";

$db = new DBconnection();


/*
|--------------------------------------------------------------------------
| Ambil ID Kategori
|--------------------------------------------------------------------------
*/

$category_id = (int) ($_GET['id'] ?? 0);

if ($category_id <= 0) {
    header("Location: index.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Ambil Data Kategori
|--------------------------------------------------------------------------
*/

$categoryResult = $db->send_query(
    "SELECT *
     FROM categories
     WHERE id = :id",
    [
        'id' => $category_id
    ]
);

$category = $categoryResult->data[0] ?? null;

if (!$category) {
    header("Location: index.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Ambil Event Berdasarkan Kategori
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

     WHERE e.category_id = :category_id
     AND e.status IN ('upcoming', 'ongoing')

     GROUP BY
        e.id,
        c.name

     ORDER BY
        e.event_date,
        e.event_time",
    [
        'category_id' => $category_id
    ]
);

$events = $eventResult->data;


/*
|--------------------------------------------------------------------------
| Function
|--------------------------------------------------------------------------
*/

function formatTanggalKategori($date)
{
    if (empty($date)) {
        return '-';
    }

    return date(
        'd M Y',
        strtotime($date)
    );
}


function formatHargaKategori($price)
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

    <title>
        <?= htmlspecialchars($category['name']) ?>
        - EventTicket
    </title>


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
                        class="nav-link"
                        href="index.php"
                    >
                        Beranda
                    </a>

                </li>

                <li class="nav-item">

                    <a
                        class="nav-link active"
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


<!-- =========================================================
     CONTENT
========================================================= -->

<div class="container py-5">


    <!-- Breadcrumb -->

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

                Kategori

            </li>

            <li
                class="breadcrumb-item active"
                aria-current="page"
            >

                <?= htmlspecialchars(
                    $category['name']
                ) ?>

            </li>

        </ol>

    </nav>


    <!-- Category Header -->

    <div class="mb-5">

        <span class="badge bg-primary-subtle text-primary mb-2">

            Kategori Event

        </span>


        <h1 class="fw-bold">

            <?= htmlspecialchars(
                $category['name']
            ) ?>

        </h1>


        <?php if (!empty($category['description'])): ?>

            <p class="text-muted mb-0">

                <?= htmlspecialchars(
                    $category['description']
                ) ?>

            </p>

        <?php else: ?>

            <p class="text-muted mb-0">

                Temukan event dalam kategori
                <?= htmlspecialchars(
                    $category['name']
                ) ?>.

            </p>

        <?php endif; ?>

    </div>


    <!-- Event Count -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="fw-bold mb-1">
                Event <?= htmlspecialchars($category['name']) ?>
            </h4>

            <p class="text-muted mb-0">

                <?= count($events) ?>
                event tersedia

            </p>

        </div>

    </div>


    <!-- Event List -->

    <?php if (!empty($events)): ?>

        <div class="row g-4">

            <?php foreach ($events as $event): ?>

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
                                style="height: 190px; object-fit: cover;"
                            >

                        <?php else: ?>

                            <div
                                class="bg-secondary-subtle d-flex align-items-center justify-content-center"
                                style="height: 190px;"
                            >

                                <div class="text-center text-muted">

                                    <i
                                        class="bi bi-image fs-1"
                                    ></i>

                                    <div class="small">
                                        Tidak ada gambar
                                    </div>

                                </div>

                            </div>

                        <?php endif; ?>


                        <!-- Body -->

                        <div class="card-body d-flex flex-column">


                            <!-- Status -->

                            <?php if ($event['status'] === 'ongoing'): ?>

                                <span
                                    class="badge bg-success align-self-start mb-2"
                                >
                                    Sedang Berlangsung
                                </span>

                            <?php else: ?>

                                <span
                                    class="badge bg-primary-subtle text-primary align-self-start mb-2"
                                >
                                    Akan Datang
                                </span>

                            <?php endif; ?>


                            <!-- Name -->

                            <h6 class="fw-bold">

                                <?= htmlspecialchars(
                                    $event['name']
                                ) ?>

                            </h6>


                            <!-- Date -->

                            <div class="small text-muted mb-1">

                                <i class="bi bi-calendar3 me-1"></i>

                                <?= formatTanggalKategori(
                                    $event['event_date']
                                ) ?>

                            </div>


                            <!-- Time -->

                            <div class="small text-muted mb-1">

                                <i class="bi bi-clock me-1"></i>

                                <?= date(
                                    'H:i',
                                    strtotime(
                                        $event['event_time']
                                    )
                                ) ?>

                                WIB

                            </div>


                            <!-- Location -->

                            <div class="small text-muted mb-3">

                                <i class="bi bi-geo-alt me-1"></i>

                                <?= htmlspecialchars(
                                    $event['location']
                                ) ?>

                            </div>


                            <!-- Price + Button -->

                            <div class="mt-auto">

                                <div
                                    class="d-flex justify-content-between align-items-end"
                                >

                                    <div>

                                        <small class="text-muted">
                                            Mulai dari
                                        </small>

                                        <div class="fw-bold text-primary">

                                            <?= formatHargaKategori(
                                                $event['min_price']
                                            ) ?>

                                        </div>

                                    </div>


                                    <a
                                        href="event_detail.php?id=<?= $event['id'] ?>"
                                        class="btn btn-primary btn-sm"
                                    >
                                        Lihat Event
                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>


    <?php else: ?>


        <!-- Tidak ada event -->

        <div class="card border-0 shadow-sm">

            <div class="card-body text-center py-5">

                <i
                    class="bi bi-calendar-x display-4 text-muted"
                ></i>


                <h5 class="fw-bold mt-3">

                    Belum ada event

                </h5>


                <p class="text-muted">

                    Belum ada event yang tersedia
                    dalam kategori ini.

                </p>


                <a
                    href="index.php"
                    class="btn btn-primary"
                >

                    <i class="bi bi-arrow-left me-1"></i>

                    Kembali ke Beranda

                </a>

            </div>

        </div>


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

                    <i
                        class="bi bi-ticket-perforated-fill me-1"
                    ></i>

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

                    <li>

                        <a
                            href="index.php#kategori"
                            class="text-white-50 text-decoration-none"
                        >
                            Kategori
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

            © 2026 EventTicket.
            All rights reserved.

        </small>

    </div>

</footer>


<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>