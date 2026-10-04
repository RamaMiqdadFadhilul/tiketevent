<?php

require_once "../../bootstrap.php";

$db = new DBconnection();

$event = new Event($db);

$events = $event->find_all();

require_once "../templates/header.php";
require_once "../templates/navbar.php";
require_once "../templates/sidebar.php";
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <h3 class="mb-0">Event</h3>
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
                        Tambah Event
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="60">No</th>
                                    <th>Nama Event</th>
                                    <th>Lokasi</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                    <th width="160">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                            <?php if (empty($events)): ?>

                                <tr>
                                    <td colspan="7" class="text-center">
                                        Belum ada event.
                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php foreach ($events as $index => $item): ?>

                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($item['name']) ?></td>
                                        <td><?= htmlspecialchars($item['location']) ?></td>
                                        <td><?= htmlspecialchars($item['event_date']) ?></td>
                                        <td><?= htmlspecialchars($item['event_time']) ?></td>
                                        <td><?= htmlspecialchars($item['status']) ?></td>
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
                                                onclick="return confirm('Hapus event ini?')"
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