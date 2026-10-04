<?php

require_once __DIR__ . "/../bootstrap.php";

$db = new DBconnection();

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
        e.event_time"
);

$events = $eventResult->data;

$categoryResult = $db->send_query(
    "SELECT *
     FROM categories
     ORDER BY name"
);

$categories = $categoryResult->data;

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
    <title><?= Konfigurasi::APP_NAME ?> - Temukan Event Favoritmu</title>
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

<?php

$activePage = 'home';

require_once __DIR__ . "/templates/navbar.php";

?>

<div class="container mt-4">
    <div class="bg-primary rounded-4 p-4 p-md-5 text-white shadow-sm">
        <div class="row align-items-center">
            <div class="col-md-8">
                <span class="badge bg-white text-primary mb-3">Event Ticketing System</span>
                <h1 class="fw-bold display-6">Temukan Event Favoritmu</h1>
                <p class="lead mb-0">Jelajahi berbagai event menarik dan pesan tiketnya dengan mudah.</p>
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

<div class="container mt-5" id="kategori">
    <div class="mb-4">
        <span class="badge bg-primary-subtle text-primary mb-2">Jelajahi</span>
        <h4 class="fw-bold mb-1">Kategori Event</h4>
        <p class="text-muted mb-0">Temukan event berdasarkan kategori favoritmu.</p>
    </div>

    <?php if (!empty($categories)): ?>

        <div class="row g-3">

            <?php foreach ($categories as $category): ?>

                <div class="col-6 col-md-4 col-lg-3">
                    <a
                        href="category.php?id=<?= $category['id'] ?>"
                        class="text-decoration-none"
                    >
                        <div class="card border-0 shadow-sm h-100">
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

        <div class="alert alert-light border">Belum ada kategori event.</div>

    <?php endif; ?>

</div>

<div class="container mt-5 mb-5" id="event">
    <div class="mb-4">
        <span class="badge bg-light text-dark border mb-2">Semua Event</span>
        <h4 class="fw-bold mb-1">Semua Event</h4>
        <p class="text-muted mb-0">Temukan berbagai event dan pesan tiketnya.</p>
    </div>

    <?php if (!empty($events)): ?>

        <div class="row g-4">

            <?php foreach ($events as $event): ?>

                <div class="col-12 col-sm-6 col-lg-3">

                    <div class="card h-100 border-0 shadow-sm">

                        <?php if (!empty($event['image'])): ?>

                            <img
                                src="../img/<?= htmlspecialchars($event['image']) ?>"
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
                                    <p class="small mb-0">Event Belum Memiliki Gambar</p>
                                </div>
                            </div>

                        <?php endif; ?>

                        <div class="card-body d-flex flex-column">

                            <?php if (!empty($event['category_name'])): ?>

                                <span class="badge bg-primary-subtle text-primary align-self-start mb-2">
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
                                <div class="d-flex justify-content-between align-items-end">
                                    <div>
                                        <small class="text-muted">Mulai dari</small>
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
                <i class="bi bi-calendar-x display-5 text-muted"></i>
                <h5 class="mt-3">Belum ada event</h5>
                <p class="text-muted mb-0">Saat ini belum ada event yang tersedia.</p>
            </div>
        </div>

    <?php endif; ?>

</div>
</body>
</html>