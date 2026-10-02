<?php

require_once "../../bootstrap.php";

$db = new DBconnection();

$event = new Event($db);

$data = $event->find_all();

require_once "../templates/header.php";
require_once "../templates/navbar.php";
require_once "../templates/sidebar.php";

?>

<main class="app-main">

    <!-- Header -->
    <div class="app-content-header">
        <div class="container-fluid">

            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Events</h3>
                </div>
            </div>

        </div>
    </div>


    <!-- Content -->
    <div class="app-content">
        <div class="container-fluid">

            <div class="card">

                <!-- Card Header -->
                <div class="card-header">
                    <h3 class="card-title">Data Event</h3>

                    <div class="card-tools">
                        <a
                            href="tambah.php"
                            class="btn btn-primary btn-sm"
                        >
                            + Tambah Event
                        </a>
                    </div>
                </div>


                <!-- Card Body -->
                <div class="card-body">

                    <?php if (empty($data)): ?>

                        <div class="alert alert-info">
                            Belum ada data event.
                        </div>

                    <?php else: ?>

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover">

                                <thead>
                                    <tr>
                                        <th style="width: 60px;">No.</th>
                                        <th>Category</th>
                                        <th>Nama Event</th>
                                        <th>Deskripsi</th>
                                        <th>Lokasi</th>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Status</th>
                                        <th style="width: 150px;">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php $no = 1; ?>

                                    <?php foreach ($data as $row): ?>

                                        <tr>

                                            <td>
                                                <?= $no++ ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['category_id']) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['name']) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['description'] ?? '') ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['location']) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['event_date']) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['event_time']) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['status']) ?>
                                            </td>

                                            <td>

                                                <a
                                                    href="edit.php?id=<?= $row['id'] ?>"
                                                    class="btn btn-warning btn-sm"
                                                >
                                                    Edit
                                                </a>

                                                <a
                                                    href="hapus.php?id=<?= $row['id'] ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Yakin ingin menghapus event ini?')"
                                                >
                                                    Hapus
                                                </a>

                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>
    </div>

</main>

<?php

require_once "../templates/footer.php";

?>