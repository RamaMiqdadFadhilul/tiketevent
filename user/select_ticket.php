<?php

session_start();

require_once "../bootstrap.php";

$db = new DBconnection();

$event_id = (int) ($_GET['event_id'] ?? $_POST['event_id'] ?? 0);

if ($event_id <= 0) {
    header("Location: index.php");
    exit;
}

$eventResult = $db->send_query(
    "SELECT *
     FROM events
     WHERE id = $1",
    [
        $event_id
    ]
);

$event = $eventResult->data[0] ?? null;

if (!$event) {
    header("Location: index.php");
    exit;
}

$ticketResult = $db->send_query(
    "SELECT *
     FROM tickets
     WHERE event_id = $1
     AND stock > 0
     ORDER BY price",
    [
        $event_id
    ]
);

$tickets = $ticketResult->data;

$error = '';

$selectedTickets = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $postedTickets = $_POST['tickets'] ?? [];

    foreach ($postedTickets as $ticket_id => $quantity) {

        $ticket_id = (int) $ticket_id;
        $quantity = (int) $quantity;

        if ($ticket_id <= 0) {
            continue;
        }

        if ($quantity > 0) {
            $selectedTickets[$ticket_id] = $quantity;
        }
    }

    if (empty($selectedTickets)) {
        $error = 'Silakan pilih minimal 1 tiket.';
    } else {
        foreach ($selectedTickets as $ticket_id => $quantity) {
            $ticketResult = $db->send_query(
                "SELECT *
                 FROM tickets
                 WHERE id = $1
                 AND event_id = $2",
                [
                    $ticket_id,
                    $event_id
                ]
            );

            $ticket = $ticketResult->data[0] ?? null;

            if (!$ticket) {
                $error = 'Tiket yang dipilih tidak valid.';
                break;
            }

            if ($quantity > (int) $ticket['stock']) {
                $error =
                    'Jumlah tiket "' .
                    $ticket['name'] .
                    '" melebihi stok yang tersedia.';
                break;
            }
        }

        if ($error === '') {
            $_SESSION['selected_tickets'] = $selectedTickets;
            $_SESSION['selected_event_id'] = $event_id;
            header(
                "Location: order_detail.php?event_id=" . $event_id
            );
            exit;
        }
    }
}

?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Tiket - <?= htmlspecialchars($event['name']) ?></title>

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
<div class="container py-5">
    <div class="mb-4">
        <a
            href="event_detail.php?id=<?= $event_id ?>"
            class="text-decoration-none text-primary"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke Detail Event
        </a>
    </div>

    <div class="mb-4">
        <h1 class="fw-bold mb-2">Pilih Tiket</h1>
        <p class="text-muted fs-5 mb-0">
            <?= htmlspecialchars($event['name']) ?>
        </p>
    </div>

    <!-- alert eror -->
    <?php if ($error !== ''): ?>
        <div
            class="alert alert-warning d-flex align-items-center"
            role="alert"
        >
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            <div>
                <?= htmlspecialchars($error) ?>
            </div>
        </div>
    <?php endif; ?>

    <form
        method="POST"
        action="select_ticket.php?event_id=<?= $event_id ?>"
    >
        <input
            type="hidden"
            name="event_id"
            value="<?= $event_id ?>"
        >
        <?php if (!empty($tickets)): ?>
            <div class="row g-4">
                <?php foreach ($tickets as $ticket): ?>
                    <?php
                    $ticket_id = (int) $ticket['id'];
                    $quantity =
                        $selectedTickets[$ticket_id] ?? 0;
                    ?>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h4 class="fw-bold mb-3">
                                            <?= htmlspecialchars(
                                                $ticket['name']
                                            ) ?>
                                        </h4>
                                        <h3 class="fw-bold text-primary mb-3">
                                            Rp<?= number_format(
                                                $ticket['price'],
                                                0,
                                                ',',
                                                '.'
                                            ) ?>
                                        </h3>
                                        <p class="text-muted mb-0">
                                            Stok:
                                            <?= (int) $ticket['stock'] ?>
                                        </p>
                                    </div>
                                    <div>
                                        <label
                                            for="ticket-<?= $ticket_id ?>"
                                            class="form-label small text-muted"
                                        >
                                            Jumlah
                                        </label>
                                        <input
                                            id="ticket-<?= $ticket_id ?>"
                                            type="number"
                                            name="tickets[<?= $ticket_id ?>]"
                                            class="form-control form-control-lg"
                                            value="<?= $quantity ?>"
                                            min="0"
                                            max="<?= (int) $ticket['stock'] ?>"
                                            style="width: 150px;"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button
                    type="submit"
                    class="btn btn-primary btn-lg px-4"
                >
                    Lanjut
                    <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        <?php else: ?>

            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i
                        class="bi bi-ticket-perforated fs-1 text-muted"
                    ></i>
                    <h5 class="fw-bold mt-3">Tiket Tidak Tersedia</h5>
                    <p class="text-muted">
                        Saat ini belum ada tiket yang tersedia
                        untuk event ini.
                    </p>
                    <a
                        href="event_detail.php?id=<?= $event_id ?>"
                        class="btn btn-primary"
                    >
                        Kembali ke Detail Event
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </form>
</div>
</body>
</html>