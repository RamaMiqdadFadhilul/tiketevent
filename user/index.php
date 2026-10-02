<?php

require_once "../bootstrap.php";

$db = new DBconnection();

$eventModel = new Event($db);

// Ambil event yang sedang berlangsung
$result = $db->send_query(
    "SELECT *
     FROM events
     WHERE status = :status
     ORDER BY event_date, event_time",
    [
        'status' => 'ongoing'
    ]
);

$events = $result->data;

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Event Ticketing System</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>


<body class="bg-light">


<!-- Navbar -->

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


<!-- Content -->

<div class="container py-5">

    <div class="text-center mb-5">

        <h1 class="fw-bold">
            Event Sedang Berlangsung
        </h1>

        <p class="text-muted">
            Temukan event dan pesan tiket favoritmu.
        </p>

    </div>


    <?php if (empty($events)): ?>

        <div class="card shadow-sm">

            <div class="card-body text-center py-5">

                <h5 class="text-muted">
                    Belum ada event yang sedang berlangsung.
                </h5>

            </div>

        </div>

    <?php else: ?>

        <div class="row g-4">

            <?php foreach ($events as $event): ?>

                <div class="col-md-6 col-lg-4">

                    <div class="card h-100 shadow-sm">

                        <!-- Event Image -->

                        <?php if (!empty($event['image'])): ?>

                            <img
                                src="<?= htmlspecialchars($event['image']) ?>"
                                class="card-img-top"
                                alt="<?= htmlspecialchars($event['name']) ?>"
                                style="height: 200px; object-fit: cover;"
                            >

                        <?php else: ?>

                            <div
                                class="bg-secondary text-white d-flex align-items-center justify-content-center"
                                style="height: 200px;"
                            >
                                <span>
                                    Tidak ada gambar
                                </span>
                            </div>

                        <?php endif; ?>


                        <div class="card-body">

                            <span class="badge bg-success mb-2">
                                Sedang Berlangsung
                            </span>


                            <h5 class="card-title fw-bold">

                                <?= htmlspecialchars(
                                    $event['name']
                                ) ?>

                            </h5>


                            <p class="card-text text-muted">

                                <?= htmlspecialchars(
                                    $event['description']
                                    ?? 'Tidak ada deskripsi.'
                                ) ?>

                            </p>


                            <div class="mb-2">

                                <small class="text-muted">

                                    📅

                                    <?= date(
                                        'd F Y',
                                        strtotime(
                                            $event['event_date']
                                        )
                                    ) ?>

                                </small>

                            </div>


                            <div class="mb-3">

                                <small class="text-muted">

                                    🕐

                                    <?= date(
                                        'H:i',
                                        strtotime(
                                            $event['event_time']
                                        )
                                    ) ?>

                                    WIB

                                </small>

                            </div>


                            <div class="mb-3">

                                <small class="text-muted">

                                    📍

                                    <?= htmlspecialchars(
                                        $event['location']
                                    ) ?>

                                </small>

                            </div>


                            <a
                                href="event_detail.php?id=<?= $event['id'] ?>"
                                class="btn btn-primary w-100"
                            >
                                Lihat Event
                            </a>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>


<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>