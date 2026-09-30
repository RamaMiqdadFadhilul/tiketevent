<?php

require_once "../../bootstrap.php";

$db = new DBconnection();

$event = new Event($db);

$data = $event->find_all();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Events</title>
</head>

<body>

<h1>Data Events</h1>

<a href="tambah.php">Tambah Event</a>

<br><br>

<table border="1" cellpadding="10">

    <tr>
        <th>ID</th>
        <th>Category</th>
        <th>Nama Event</th>
        <th>Deskripsi</th>
        <th>Lokasi</th>
        <th>Tanggal</th>
        <th>Waktu</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php foreach ($data as $row): ?>

    <tr>

        <td><?= $row['id']; ?></td>

        <td><?= $row['category_id']; ?></td>

        <td><?= htmlspecialchars($row['name']); ?></td>

        <td><?= htmlspecialchars($row['description'] ?? ''); ?></td>

        <td><?= htmlspecialchars($row['location']); ?></td>

        <td><?= $row['event_date']; ?></td>

        <td><?= $row['event_time']; ?></td>

        <td><?= $row['status']; ?></td>

        <td>
            <a href="edit.php?id=<?= $row['id']; ?>">
                Edit
            </a>

            |

            <a
                href="hapus.php?id=<?= $row['id']; ?>"
                onclick="return confirm('Yakin ingin menghapus event ini?')"
            >
                Hapus
            </a>
        </td>

    </tr>

    <?php endforeach; ?>

</table>

</body>
</html>