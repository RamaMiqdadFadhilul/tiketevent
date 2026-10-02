<?php

require_once "../../bootstrap.php";

$db = new DBconnection();
$ticket = new Ticket($db);

$tickets = $ticket->find_all();

?>

<?php require_once "../templates/header.php"; ?>
<?php require_once "../templates/navbar.php"; ?>
<?php require_once "../templates/sidebar.php"; ?>

<div class="content-wrapper">

    <!-- Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tickets</h1>
                </div>
            </div>
        </div>
    </section>


    <!-- Content -->
    <section class="content">
        <div class="container-fluid">

            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">
                        Daftar Tiket
                    </h3>

                    <div class="card-tools">
                        <a href="create.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i>
                            Tambah Tiket
                        </a>
                    </div>
                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover">

                            <thead>
                                <tr>
                                    <th width="60">ID</th>
                                    <th>Event</th>
                                    <th>Nama Tiket</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>

                            <?php if (empty($tickets)): ?>

                                <tr>
                                    <td colspan="6" class="text-center">
                                        Belum ada tiket.
                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php foreach ($tickets as $item): ?>

                                    <?php

                                    $eventResult = $db->send_query(
                                        "SELECT name FROM events WHERE id = :id",
                                        ['id' => $item['event_id']]
                                    );

                                    $event = $eventResult->data[0]['name']
                                        ?? 'Event tidak ditemukan';

                                    ?>

                                    <tr>

                                        <td>
                                            <?= $item['id'] ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($event) ?>
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

                                            <?php if ($item['stock'] > 0): ?>

                                                <span class="badge badge-success">
                                                    <?= $item['stock'] ?>
                                                </span>

                                            <?php else: ?>

                                                <span class="badge badge-danger">
                                                    Habis
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <td>

                                            <a
                                                href="update.php?id=<?= $item['id'] ?>"
                                                class="btn btn-warning btn-sm"
                                            >
                                                <i class="fas fa-edit"></i>
                                                Edit
                                            </a>

                                            <a
                                                href="delete.php?id=<?= $item['id'] ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Yakin ingin menghapus tiket ini?')"
                                            >
                                                <i class="fas fa-trash"></i>
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
    </section>

</div>

<?php require_once "../templates/footer.php"; ?>