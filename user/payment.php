<?php

session_start();

require_once __DIR__ . "/../bootstrap.php";

$db = new DBconnection();

$event_id = (int) ($_POST['event_id'] ?? 0);

$ticket_quantities = $_POST['tickets'] ?? [];

$customer_name = trim($_POST['customer_name'] ?? '');
$customer_email = trim($_POST['customer_email'] ?? '');
$customer_phone = trim($_POST['customer_phone'] ?? '');

if (
    $event_id <= 0 ||
    empty($ticket_quantities) ||
    $customer_name === '' ||
    $customer_email === '' ||
    $customer_phone === ''
) {
    header("Location: index.php");
    exit;
}

$eventModel = new Event($db);

$event = $eventModel->find_by_id($event_id);

if (!$event) {
    header("Location: index.php");
    exit;
}

if (!in_array($event['status'], ['upcoming', 'ongoing'])) {

    header(
        "Location: event_detail.php?id=" . $event_id
    );

    exit;
}

$selected_tickets = [];

foreach ($ticket_quantities as $ticket_id => $quantity) {
    $ticket_id = (int) $ticket_id;
    $quantity = (int) $quantity;

    if ($ticket_id > 0 && $quantity > 0) {
        $selected_tickets[$ticket_id] = $quantity;
    }
}

if (empty($selected_tickets)) {
    header(
        "Location: select_ticket.php?event_id=" . $event_id
    );
    exit;
}

$ticket_ids = array_keys($selected_tickets);

$placeholders = [];

$params = [$event_id];

foreach ($ticket_ids as $index => $ticket_id) {
    $placeholder = '$' . ($index + 2);
    $placeholders[] = $placeholder;
    $params[] = (int) $ticket_id;
}

$result = $db->send_query(
    "SELECT *
     FROM tickets
     WHERE event_id = $1
     AND id IN (" . implode(',', $placeholders) . ")
     ORDER BY price",
    $params
);

if (
    !$result->success ||
    empty($result->data)
) {
    header(
        "Location: select_ticket.php?event_id=" . $event_id
    );
    exit;
}

$tickets = $result->data;

$valid_selected_tickets = [];

foreach ($tickets as $ticket) {
    $ticket_id = (int) $ticket['id'];
    $quantity =
        $selected_tickets[$ticket_id] ?? 0;

    if ($quantity > (int) $ticket['stock']) {
        $quantity = (int) $ticket['stock'];
    }

    if ($quantity > 0) {
        $valid_selected_tickets[$ticket_id] = $quantity;
    }
}

$selected_tickets = $valid_selected_tickets;

if (empty($selected_tickets)) {
    header(
        "Location: select_ticket.php?event_id=" . $event_id
    );
    exit;
}

$total_quantity = 0;

$total_amount = 0;

foreach ($tickets as $ticket) {
    $quantity =
        $selected_tickets[$ticket['id']] ?? 0;

    if ($quantity <= 0) {
        continue;
    }

    $total_quantity += $quantity;
    $total_amount +=
        (float) $ticket['price'] * $quantity;
}

$categoryResult = $db->send_query(
    "SELECT name
     FROM categories
     WHERE id = $1",
    [
        $event['category_id']
    ]
);

$category_name =
    $categoryResult->data[0]['name'] ?? 'Event';

$_SESSION['checkout'] = [
    'event_id' => $event_id,
    'tickets' => $selected_tickets,
    'customer_name' => $customer_name,
    'customer_email' => $customer_email,
    'customer_phone' => $customer_phone
];

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>Pembayaran - <?= htmlspecialchars($event['name']) ?></title>

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

<?php

$activePage = 'event';

require_once __DIR__ . "/templates/navbar.php";

?>

<div class="container py-5">

    <nav
        aria-label="breadcrumb"
        class="mb-4"
    >
        <ol class="breadcrumb mb-0">
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
                    href="event_detail.php?id=<?= $event_id ?>"
                    class="text-decoration-none"
                >
                    <?= htmlspecialchars($event['name']) ?>
                </a>
            </li>
            <li class="breadcrumb-item active">
                Pembayaran
            </li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <span class="badge bg-primary mb-2">
                <?= htmlspecialchars($category_name) ?>
            </span>
            <h3 class="fw-bold mb-3">
                <?= htmlspecialchars($event['name']) ?>
            </h3>
            <div class="d-flex flex-wrap align-items-center gap-3 text-muted">
                <div class="d-flex align-items-center">
                    <i class="bi bi-calendar-event text-primary me-2"></i>
                    <?= date(
                        'd M Y',
                        strtotime($event['event_date'])
                    ) ?>
                </div>
                <span class="text-secondary">•</span>
                <div class="d-flex align-items-center">
                    <i
                        class="bi bi-clock text-primary me-2"
                    ></i>
                    <?= date(
                        'H:i',
                        strtotime($event['event_time'])
                    ) ?>
                    WIB
                </div>
                <span class="text-secondary">•</span>
                <div class="d-flex align-items-center">
                    <i class="bi bi-geo-alt text-primary me-2"></i>
                    <?= htmlspecialchars($event['location']) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-1">Metode Pembayaran</h4>
                    <p class="text-muted mb-4">
                        Pilih metode pembayaran yang ingin digunakan.
                    </p>
                    <form
                        action="process_payment.php"
                        method="POST"
                    >
                        <input
                            type="hidden"
                            name="event_id"
                            value="<?= $event_id ?>"
                        >
                        <?php foreach (
                            $selected_tickets as $ticket_id => $quantity
                        ): ?>
                            <input
                                type="hidden"
                                name="tickets[<?= $ticket_id ?>]"
                                value="<?= $quantity ?>"
                            >
                        <?php endforeach; ?>
                        <input
                            type="hidden"
                            name="customer_name"
                            value="<?= htmlspecialchars($customer_name) ?>"
                        >
                        <input
                            type="hidden"
                            name="customer_email"
                            value="<?= htmlspecialchars($customer_email) ?>"
                        >
                        <input
                            type="hidden"
                            name="customer_phone"
                            value="<?= htmlspecialchars($customer_phone) ?>"
                        >
                        <div class="mb-3">
                            <input
                                type="radio"
                                class="btn-check"
                                name="payment_method"
                                id="transfer_bank"
                                value="transfer_bank"
                                required
                            >
                            <label
                                for="transfer_bank"
                                class="btn btn-outline-primary w-100 text-start p-4"
                            >
                                <div
                                    class="d-flex align-items-center"
                                >
                                    <i
                                        class="bi bi-bank fs-2 me-4"
                                    ></i>
                                    <div>
                                        <div
                                            class="fw-bold fs-5"
                                        >
                                            Transfer Bank
                                        </div>
                                        <div>
                                            Pembayaran melalui transfer bank
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="mb-3">
                            <input
                                type="radio"
                                class="btn-check"
                                name="payment_method"
                                id="e_wallet"
                                value="e_wallet"
                            >
                            <label
                                for="e_wallet"
                                class="btn btn-outline-primary w-100 text-start p-4"
                            >
                                <div
                                    class="d-flex align-items-center"
                                >
                                    <i
                                        class="bi bi-wallet2 fs-2 me-4"
                                    ></i>
                                    <div>
                                        <div
                                            class="fw-bold fs-5"
                                        >
                                            E-Wallet
                                        </div>
                                        <div>
                                            GoPay, OVO, DANA, dan lainnya
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="mb-4">
                            <input
                                type="radio"
                                class="btn-check"
                                name="payment_method"
                                id="qris"
                                value="qris"
                            >
                            <label
                                for="qris"
                                class="btn btn-outline-primary w-100 text-start p-4"
                            >
                                <div
                                    class="d-flex align-items-center"
                                >
                                    <i
                                        class="bi bi-qr-code fs-2 me-4"
                                    ></i>
                                    <div>
                                        <div
                                            class="fw-bold fs-5"
                                        >
                                            QRIS
                                        </div>
                                        <div>Pembayaran menggunakan QRIS</div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="alert alert-info d-flex align-items-start">
                            <i
                                class="bi bi-info-circle me-2 mt-1"
                            ></i>
                            <div>
                                Pembayaran pada sistem ini masih bersifat
                                <strong>simulasi</strong>.
                            </div>
                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary btn-lg w-100"
                        >
                            <i
                                class="bi bi-credit-card me-2"
                            ></i>
                            Bayar Rp <?= number_format(
                                $total_amount,
                                0,
                                ',',
                                '.'
                            ) ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1">Ringkasan Pesanan</h5>
                    <p class="text-muted mb-4">
                        <?= htmlspecialchars($event['name']) ?>
                    </p>
                    <?php foreach ($tickets as $ticket): ?>
                        <?php
                        $quantity =
                            $selected_tickets[$ticket['id']] ?? 0;

                        if ($quantity <= 0) {
                            continue;
                        }

                        $subtotal =
                            (float) $ticket['price'] * $quantity;

                        ?>

                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <div class="fw-semibold">
                                    <?= htmlspecialchars(
                                        $ticket['name']
                                    ) ?>
                                </div>
                                <small class="text-muted">
                                    <?= $quantity ?> ×
                                    Rp <?= number_format(
                                        $ticket['price'],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>
                                </small>
                            </div>

                            <div class="fw-semibold">
                                Rp <?= number_format(
                                    $subtotal,
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Tiket</span>
                        <span class="fw-semibold">
                            <?= $total_quantity ?> tiket
                        </span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Total Pembayaran</span>
                        <span
                            class="fw-bold text-primary fs-5"
                        >
                            Rp <?= number_format(
                                $total_amount,
                                0,
                                ',',
                                '.'
                            ) ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-person me-2"></i>
                        Data Pemesan
                    </h6>

                    <div class="mb-2">
                        <small class="text-muted">Nama</small>
                        <div class="fw-semibold">
                            <?= htmlspecialchars(
                                $customer_name
                            ) ?>
                        </div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Email</small>
                        <div>
                            <?= htmlspecialchars(
                                $customer_email
                            ) ?>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted">No. Telepon</small>
                        <div>
                            <?= htmlspecialchars(
                                $customer_phone
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>