<?php

require_once __DIR__ . "/../bootstrap.php";

$db = new DBconnection();

$category_id = (int) ($_GET['id'] ?? 0);

if ($category_id <= 0) {
    header("Location: index.php");
    exit;
}

$categoryResult = $db->send_query(
    "SELECT *
     FROM categories
     WHERE id = $1",
    [
        $category_id
    ]
);

$category = $categoryResult->data[0] ?? null;

if (!$category) {
    header("Location: index.php");
    exit;
}

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
     WHERE e.category_id = $1
       AND e.status IN ('upcoming', 'ongoing')
     GROUP BY
        e.id,
        c.name
     ORDER BY
        e.event_date,
        e.event_time",
    [
        $category_id
    ]
);

$events = $eventResult->data;

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
    <title><?= htmlspecialchars($category['name']) ?> - <?= Konfigurasi::APP_NAME ?></title>
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

$activePage = 'category';

require_once __DIR__ . "/templates/navbar.php";

?>

<div class="container py-5">
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
            <li class="breadcrumb-item">Kategori</li>
            <li class="breadcrumb-item active" aria-current="page">
                <?= htmlspecialchars($category['name']) ?>
            </li>
        </ol>
    </nav>
    <div class="mb-5">
        <span class="badge bg-primary-subtle text-primary mb-2">Kategori Event</span>
        <h1 class="fw-bold"><?= htmlspecialchars($category['name']) ?></h1>
        <?php if (!empty($category['description'])): ?>
            <p class="text-muted mb-0">
                <?= htmlspecialchars($category['description']) ?>
            </p>
        <?php else: ?>
            <p class="text-muted mb-0">
                Temukan event dalam kategori
                <?= htmlspecialchars($category['name']) ?>.
            </p>
        <?php endif; ?>
    </div>
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Event<?= htmlspecialchars($category['name']) ?></h4>
        <p class="text-muted mb-0">
            <?= count($events) ?> event tersedia
        </p>
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

                            <?php if ($event['status'] === 'ongoing'): ?>

                                <span class="badge bg-success align-self-start mb-2">
                                    Sedang Berlangsung
                                </span>

                            <?php else: ?>

                                <span class="badge bg-primary-subtle text-primary align-self-start mb-2">
                                    Akan Datang
                                </span>

                            <?php endif; ?>

                            <h6 class="fw-bold">
                                <?= htmlspecialchars($event['name']) ?>
                            </h6>

                            <div class="small text-muted mb-1">
                                <i class="bi bi-calendar3 me-1"></i>
                                <?= formatTanggalKategori(
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
                                <?= htmlspecialchars($event['location']) ?>
                            </div>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-end">
                                    <div>
                                        <small class="text-muted">Mulai dari</small>
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

        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-calendar-x display-4 text-muted"></i>
                <h5 class="fw-bold mt-3">Belum ada event</h5>
                <p class="text-muted">Belum ada event yang tersedia dalam kategori ini.</p>
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
</body>
</html>