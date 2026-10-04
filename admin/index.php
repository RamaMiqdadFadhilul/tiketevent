<?php

require_once "../bootstrap.php";

$db = new DBconnection();

$event = new Event($db);
$category = new Category($db);
$ticket = new Ticket($db);

$total_event = count($event->find_all());
$total_category = count($category->find_all());
$total_ticket = count($ticket->find_all());

$result = $db->send_query(
    "SELECT COALESCE(SUM(quantity), 0) AS total
     FROM order_items
     JOIN orders
        ON orders.id = order_items.order_id
     WHERE orders.status = 'paid'"
);

$total_ticket_sold = $result->data[0]['total'] ?? 0;

$result = $db->send_query(
    "SELECT COALESCE(SUM(total_amount), 0) AS total
     FROM orders
     WHERE status = 'paid'"
);

$total_revenue = $result->data[0]['total'] ?? 0;

$result = $db->send_query(
    "SELECT *
     FROM events
     WHERE event_date >= CURRENT_DATE
     AND status = 'upcoming'
     ORDER BY event_date ASC, event_time ASC
     LIMIT 5"
);

$upcoming_events = $result->data;

$result = $db->send_query(
    "SELECT *
     FROM orders
     ORDER BY created_at DESC
     LIMIT 5"
);

$latest_orders = $result->data;

require_once "templates/header.php";
require_once "templates/navbar.php";
require_once "templates/sidebar.php";

?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <h3 class="mb-0">Dashboard</h3>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-primary">
                        <div class="inner">
                            <h3><?= $total_event ?></h3>
                            <p>Total Event</p>
                        </div>
                        <a
                            href="events/index.php"
                            class="small-box-footer"
                        >
                            Kelola Event
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-success">
                        <div class="inner">
                            <h3><?= $total_category ?> </h3>
                            <p>Total Category</p>
                        </div>
                        <a
                            href="categories/index.php"
                            class="small-box-footer"
                        >
                            Kelola Category
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-warning">
                        <div class="inner">
                            <h3><?= $total_ticket ?></h3>
                            <p>Total Ticket</p>
                        </div>
                        <a
                            href="tickets/index.php"
                            class="small-box-footer"
                        >
                            Kelola Ticket
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-danger">
                        <div class="inner">
                            <h3><?= $total_ticket_sold ?></h3>
                            <p>Tiket Terjual</p>
                        </div>
                        <a
                            href="orders/index.php"
                            class="small-box-footer"
                        >
                            Lihat Transaksi
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Total Pendapatan</h3>
                        </div>
                        <div class="card-body">
                            <h2 class="text-success">
                                Rp<?= number_format(
                                    $total_revenue,
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </h2>
                            <p class="text-muted mb-0">Total pendapatan dari transaksi yang sudah dibayar.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Event Akan Datang</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($upcoming_events)): ?>
                                <p class="text-muted mb-0">Belum ada event yang akan datang.</p>
                            <?php else: ?>
                                <div class="list-group">
                                    <?php foreach ($upcoming_events as $event_item): ?>
                                        <a
                                            href="events/edit.php?id=<?= $event_item['id'] ?>"
                                            class="list-group-item list-group-item-action"
                                        >
                                            <div class="d-flex justify-content-between">
                                                <strong>
                                                    <?= htmlspecialchars(
                                                        $event_item['name']
                                                    ) ?>
                                                </strong>
                                                <span class="badge text-bg-primary">
                                                    <?= date(
                                                        'd-m-Y',
                                                        strtotime(
                                                            $event_item['event_date']
                                                        )
                                                    ) ?>
                                                </span>
                                            </div>
                                            <small class="text-muted">
                                                <?= htmlspecialchars(
                                                    $event_item['location']
                                                ) ?>
                                            </small>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Transaksi Terbaru</h3>
                            <div class="card-tools">
                                <a
                                    href="orders/index.php"
                                    class="btn btn-sm btn-primary"
                                >
                                    Lihat Semua
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th width="50">#</th>
                                            <th>Kode Order</th>
                                            <th>Customer</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                            <th width="80">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($latest_orders)): ?>
                                            <tr>
                                                <td
                                                    colspan="7"
                                                    class="text-center text-muted"
                                                >
                                                    Belum ada transaksi.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach (
                                                $latest_orders as $index => $order
                                            ): ?>
                                                <tr>
                                                    <td><?= $index + 1 ?>
                                                    </td>
                                                    <td>
                                                        <strong>
                                                            <?= htmlspecialchars(
                                                                $order['order_code']
                                                            ) ?>
                                                        </strong>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars(
                                                            $order['customer_name']
                                                        ) ?>
                                                    </td>
                                                    <td>
                                                        Rp<?= number_format(
                                                            $order['total_amount'],
                                                            0,
                                                            ',',
                                                            '.'
                                                        ) ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $badge = match (
                                                            $order['status']
                                                        ) {
                                                            'paid' =>
                                                                'bg-success',

                                                            'pending' =>
                                                                'bg-warning text-dark',

                                                            'cancelled' =>
                                                                'bg-danger',

                                                            default =>
                                                                'bg-secondary'
                                                        };
                                                        ?>
                                                        <span class="badge <?= $badge ?>">
                                                            <?= htmlspecialchars(
                                                                ucfirst(
                                                                    $order['status']
                                                                )
                                                            ) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?= date(
                                                            'd-m-Y H:i',
                                                            strtotime(
                                                                $order['created_at']
                                                            )
                                                        ) ?>
                                                    </td>
                                                    <td>
                                                        <a
                                                            href="orders/detail.php?id=<?= $order['id'] ?>"
                                                            class="btn btn-sm btn-primary"
                                                        >
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php

require_once "templates/footer.php";

?>