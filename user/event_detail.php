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


$result = $db->send_query(
    "SELECT MIN(price) AS min_price
     FROM tickets
     WHERE event_id = $1
     AND stock > 0",
    [
        $id
    ]
);

$min_price = $result->data[0]['min_price'] ?? null;

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
    <title><?= htmlspecialchars($event['name']) ?></title>
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
            href="index.php"
            class="text-decoration-none text-muted"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke Event
        </a>
    </div>
    <div class="card border-0 shadow-sm overflow-hidden mb-4">
        <div class="row g-0">
            <div class="col-lg-5">
                <?php if (!empty($event['image'])): ?>
                    <img
                        src="../img/<?= htmlspecialchars($event['image']) ?>"
                        class="w-100 h-100"
                        alt="<?= htmlspecialchars($event['name']) ?>"
                        style="
                            object-fit: cover;
                            min-height: 430px;
                        "
                    >
                <?php else: ?>

                    <div
                        class="bg-secondary text-white d-flex align-items-center justify-content-center h-100"
                        style="min-height: 430px;"
                    >
                        <div class="text-center">
                            <i class="bi bi-image fs-1"></i>
                            <p class="mt-2 mb-0">Tidak ada gambar</p>
                        </div>
                    </div>

                <?php endif; ?>

            </div>

            <div class="col-lg-7">
                <div class="p-4 p-lg-5">

                    <?php if ($category): ?>

                        <div class="mb-3">
                            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                <i class="bi bi-grid me-1"></i>
                                <?= htmlspecialchars(
                                    $category['name']
                                ) ?>
                            </span>
                        </div>

                    <?php endif; ?>

                    <h1 class="fw-bold mb-4">
                        <?= htmlspecialchars(
                            $event['name']
                        ) ?>
                    </h1>
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-calendar3 text-primary fs-4 me-3"></i>
                                <div>
                                    <small class="text-muted d-block mb-1">Tanggal</small>
                                    <span class="fw-semibold">
                                        <?= date(
                                            'd M Y',
                                            strtotime(
                                                $event['event_date']
                                            )
                                        ) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <i
                                    class="bi bi-clock text-primary fs-4 me-3"
                                ></i>
                                <div>
                                    <small class="text-muted d-block mb-1">Waktu</small>
                                    <span class="fw-semibold">
                                        <?= date(
                                            'H:i',
                                            strtotime(
                                                $event['event_time']
                                            )
                                        ) ?>
                                        WIB
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-geo-alt text-primary fs-4 me-3"></i>
                                <div>
                                    <small class="text-muted d-block mb-1">Lokasi</small>
                                    <span class="fw-semibold">
                                        <?= htmlspecialchars(
                                            $event['location']
                                        ) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4">
                    <h5 class="fw-bold mb-3">Deskripsi</h5>
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
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <?php if ($min_price !== null): ?>
                        <small class="text-muted">Harga tiket mulai dari</small>
                        <h2 class="fw-bold text-primary mb-0 mt-1">
                            Rp<?= number_format(
                                $min_price,
                                0,
                                ',',
                                '.'
                            ) ?>
                        </h2>
                    <?php else: ?>

                        <small class="text-muted">Ketersediaan tiket</small>
                        <h5 class="text-danger fw-bold mb-0 mt-1">Tiket tidak tersedia</h5>

                    <?php endif; ?>

                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <?php if ($min_price !== null): ?>
                        <a
                            href="select_ticket.php?event_id=<?= $event['id'] ?>"
                            class="btn btn-primary btn-lg px-4"
                        >
                            Pesan Tiket
                            <i
                                class="bi bi-arrow-right ms-1"
                            ></i>
                        </a>
                    <?php else: ?>
                        <button
                            class="btn btn-secondary btn-lg px-4"
                            disabled
                        >
                            Tiket Tidak Tersedia
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>