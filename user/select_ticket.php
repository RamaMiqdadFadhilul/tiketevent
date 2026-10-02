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

$result = $db->send_query(
    "SELECT *
     FROM tickets
     WHERE event_id = :event_id
     ORDER BY price",
    ['event_id' => $event_id]
);

$tickets = $result->data;

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Pilih Tiket - <?= htmlspecialchars($event['name']) ?></title>

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

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <div class="row text-center">

                <div class="col-4">
                    <div class="fw-bold text-primary">
                        1. Pilih Kategori
                    </div>
                </div>

                <div class="col-4">
                    <div class="text-muted">
                        2. Detail Pesanan
                    </div>
                </div>

                <div class="col-4">
                    <div class="text-muted">
                        3. Metode Pembayaran
                    </div>
                </div>

            </div>

        </div>

    </div>

    <div class="mb-4">

        <a
            href="event_detail.php?id=<?= $event_id ?>"
            class="btn btn-outline-secondary mb-3"
        >
            ← Kembali
        </a>

        <h2 class="fw-bold">
            Pilih Kategori Tiket
        </h2>

        <p class="text-muted">
            <?= htmlspecialchars($event['name']) ?>
        </p>

    </div>

    <?php if (empty($tickets)): ?>

        <div class="alert alert-warning">
            Belum ada tiket untuk event ini.
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

                    <div class="col-md-6">

                        <div class="card shadow-sm h-100">

                            <div class="card-body p-4">

                                <h5 class="fw-bold mb-2">
                                    <?= htmlspecialchars(
                                        $ticket['name']
                                    ) ?>
                                </h5>

                                <h4 class="text-primary fw-bold">

                                    Rp<?= number_format(
                                        $ticket['price'],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>

                                </h4>

                                <hr>

                                <?php if ($ticket['stock'] > 0): ?>

                                    <div class="d-flex justify-content-between align-items-center">

                                        <div>

                                            <small class="text-muted">
                                                Stok tersedia
                                            </small>

                                            <div class="fw-bold">
                                                <?= $ticket['stock'] ?>
                                            </div>

                                        </div>

                                        <div
                                            class="input-group"
                                            style="width: 150px;"
                                        >

                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary btn-minus"
                                                data-ticket-id="<?= $ticket['id'] ?>"
                                            >
                                                −
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
                                                +
                                            </button>

                                        </div>

                                    </div>

                                <?php else: ?>

                                    <div class="text-danger fw-bold">
                                        Tiket habis
                                    </div>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

            <div class="card shadow-sm mt-4">

                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h5 class="fw-bold mb-1">
                                Total
                            </h5>

                            <span
                                class="text-muted"
                                id="total-item"
                            >
                                0 tiket
                            </span>

                        </div>

                        <h3
                            class="fw-bold text-primary mb-0"
                            id="total-price"
                        >
                            Rp0
                        </h3>

                    </div>

                    <button
                        type="submit"
                        id="continue-button"
                        class="btn btn-primary btn-lg w-100 mt-4"
                        disabled
                    >
                        Lanjut
                    </button>

                </div>

            </div>

        </form>

    <?php endif; ?>

</div>

<script>

const quantities = document.querySelectorAll('.quantity');
const totalPrice = document.getElementById('total-price');
const totalItem = document.getElementById('total-item');
const continueButton = document.getElementById('continue-button');

function updateTotal() {

    let total = 0;
    let item = 0;

    quantities.forEach(function(input) {

        const quantity = parseInt(input.value);
        const price = parseFloat(input.dataset.price);

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

document
    .querySelectorAll('.btn-plus')
    .forEach(function(button) {

        button.addEventListener('click', function() {

            const ticketId =
                this.dataset.ticketId;

            const input =
                document.querySelector(
                    `.quantity[data-ticket-id="${ticketId}"]`
                );

            const max =
                parseInt(input.max);

            let value =
                parseInt(input.value);

            if (value < max) {
                input.value = value + 1;
            }

            updateTotal();

        });

    });

document
    .querySelectorAll('.btn-minus')
    .forEach(function(button) {

        button.addEventListener('click', function() {

            const ticketId =
                this.dataset.ticketId;

            const input =
                document.querySelector(
                    `.quantity[data-ticket-id="${ticketId}"]`
                );

            let value =
                parseInt(input.value);

            if (value > 0) {
                input.value = value - 1;
            }

            updateTotal();

        });

    });

updateTotal();

</script>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>
