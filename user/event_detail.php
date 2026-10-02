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

$result = $db->send_query(
    "SELECT MIN(price) AS min_price
     FROM tickets
     WHERE event_id = :event_id
     AND stock > 0",
    ['event_id' => $id]
);

$min_price = $result->data[0]['min_price'] ?? null;

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title><?= htmlspecialchars($event['name']) ?></title>

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

    <a
        href="index.php"
        class="btn btn-outline-secondary mb-4"
    >
        ← Kembali ke Event
    </a>

    <div class="card shadow-sm mb-5">

        <div class="row g-0">

            <div class="col-md-5">

                <?php if (!empty($event['image'])): ?>

                    <img
                        src="<?= htmlspecialchars($event['image']) ?>"
                        class="img-fluid rounded-start w-100 h-100"
                        alt="<?= htmlspecialchars($event['name']) ?>"
                        style="object-fit: cover; min-height: 400px;"
                    >

                <?php else: ?>

                    <div
                        class="bg-secondary text-white d-flex align-items-center justify-content-center rounded-start h-100"
                        style="min-height: 400px;"
                    >
                        Tidak ada gambar
                    </div>

                <?php endif; ?>

            </div>

            <div class="col-md-7">

                <div class="card-body p-4">

                    <span class="badge bg-success mb-3">
                        Sedang Berlangsung
                    </span>

                    <h1 class="fw-bold mb-4">
                        <?= htmlspecialchars($event['name']) ?>
                    </h1>

                    <div class="mb-3">

                        <h6 class="fw-bold">
                            📅 Tanggal
                        </h6>

                        <p class="text-muted">
                            <?= date(
                                'd F Y',
                                strtotime($event['event_date'])
                            ) ?>
                        </p>

                    </div>

                    <div class="mb-3">

                        <h6 class="fw-bold">
                            🕐 Waktu
                        </h6>

                        <p class="text-muted">
                            <?= date(
                                'H:i',
                                strtotime($event['event_time'])
                            ) ?>
                            WIB
                        </p>

                    </div>

                    <div class="mb-4">

                        <h6 class="fw-bold">
                            📍 Lokasi
                        </h6>

                        <p class="text-muted">
                            <?= htmlspecialchars($event['location']) ?>
                        </p>

                    </div>

                    <hr>

                    <h5 class="fw-bold mt-4">
                        Deskripsi
                    </h5>

                    <p class="text-muted">
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

    <div class="card shadow-sm">

        <div class="card-body p-4">

            <div class="row align-items-center">

                <div class="col-md-7">

                    <p class="text-muted mb-1">
                        Harga mulai dari
                    </p>

                    <?php if ($min_price !== null): ?>

                        <h2 class="fw-bold text-primary mb-0">
                            Rp<?= number_format(
                                $min_price,
                                0,
                                ',',
                                '.'
                            ) ?>
                        </h2>

                    <?php else: ?>

                        <h5 class="text-danger mb-0">
                            Tiket tidak tersedia
                        </h5>

                    <?php endif; ?>

                </div>

                <div class="col-md-5 text-md-end mt-3 mt-md-0">

                    <?php if ($min_price !== null): ?>

                        <a
                            href="select_ticket.php?event_id=<?= $event['id'] ?>"
                            class="btn btn-primary btn-lg px-4"
                        >
                            Pesan Sekarang
                        </a>

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

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>
