<?php

require_once "../../bootstrap.php";

$db = new DBconnection();

$ticket = new Ticket($db);
$event = new Event($db);

$tickets = $ticket->find_all();
$events = $event->find_all();

$event_names = [];

foreach ($events as $item) {
    $event_names[$item['id']] = $item['name'];
}

require_once "../templates/header.php";
require_once "../templates/navbar.php";
require_once "../templates/sidebar.php";
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <h3 class="mb-0">Ticket</h3>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-end align-items-center">
                    <a
                        href="create.php"
                        class="btn btn-primary"
                    >
                        Tambah Ticket
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="60">No</th>
                                    <th>Event</th>
                                    <th>Nama Ticket</th>
                                    <th>Harga</th>
                                    <th>Stock</th>
                                    <th width="160">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                            <?php if (empty($tickets)): ?>

                                <tr>
                                    <td colspan="6" class="text-center">
                                        Belum ada ticket.
                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php foreach ($tickets as $index => $item): ?>

                                    <tr>
                                        <td>
                                            <?= $index + 1 ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars(
                                                $event_names[$item['event_id']] ?? '-'
                                            ) ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($item['name']) ?>
                                        </td>
                                        <td>
                                            Rp <?= number_format(
                                                $item['price'],
                                                0,
                                                ',',
                                                '.'
                                            ) ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($item['stock']) ?>
                                        </td>
                                        <td>
                                            <a
                                                href="update.php?id=<?= $item['id'] ?>"
                                                class="btn btn-warning btn-sm"
                                            >
                                                Edit
                                            </a>
                                            <a
                                                href="delete.php?id=<?= $item['id'] ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Hapus ticket ini?')"
                                            >
                                                Hapus
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
</main>