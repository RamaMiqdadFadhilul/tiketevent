<?php

require_once "../../bootstrap.php";

$db = new DBconnection();
$ticket = new Ticket($db);

$tickets = $ticket->find_all();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Data Ticket</title>

</head>

<body>

<h1>Data Ticket</h1>

<a href="create.php">
    + Tambah Ticket
</a>

<br>
<br>

<?php if (isset($_GET['error'])): ?>

    <p style="color: red;">
        <?= htmlspecialchars($_GET['error']) ?>
    </p>

<?php endif; ?>


<?php if (empty($tickets)): ?>

    <p>
        Belum ada data ticket.
    </p>

<?php else: ?>

    <table border="1" cellpadding="8" cellspacing="0">

        <thead>

            <tr>

                <th>No.</th>
                <th>Event</th>
                <th>Nama Ticket</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>

            </tr>

        </thead>

        <tbody>

        <?php $no = 1; ?>

        <?php foreach ($tickets as $row): ?>

            <?php

            $eventResult = $db->send_query(
                "SELECT name
                 FROM events
                 WHERE id = :id",
                [
                    'id' => $row['event_id']
                ]
            );

            $eventName = $eventResult->data[0]['name'] ?? '-';

            ?>

            <tr>

                <td>
                    <?= $no++ ?>
                </td>

                <td>
                    <?= htmlspecialchars($eventName) ?>
                </td>

                <td>
                    <?= htmlspecialchars($row['name']) ?>
                </td>

                <td>
                    Rp <?= number_format(
                        $row['price'],
                        0,
                        ',',
                        '.'
                    ) ?>
                </td>

                <td>
                    <?= htmlspecialchars($row['stock']) ?>
                </td>

                <td>

                    <a href="update.php?id=<?= $row['id'] ?>">
                        Edit
                    </a>

                    |

                    <a
                        href="delete.php?id=<?= $row['id'] ?>"
                        onclick="return confirm('Yakin ingin menghapus ticket ini?')"
                    >
                        Hapus
                    </a>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

<?php endif; ?>

</body>

</html>