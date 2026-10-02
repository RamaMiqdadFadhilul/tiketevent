<?php

require_once "../../bootstrap.php";

$db = new DBconnection();
$ticket = new Ticket($db);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $event_id = (int) $_POST['event_id'];
    $name = trim($_POST['name']);
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];

    if ($event_id <= 0) {
        $error = 'Event wajib dipilih.';
    } elseif ($name === '') {
        $error = 'Nama ticket wajib diisi.';
    } elseif ($price < 0) {
        $error = 'Harga tidak boleh kurang dari 0.';
    } elseif ($stock < 0) {
        $error = 'Stok tidak boleh kurang dari 0.';
    } else {

        $respon = $ticket->insert([
            'event_id' => $event_id,
            'name' => $name,
            'price' => $price,
            'stock' => $stock
        ]);

        if ($respon->success) {
            header('Location: index.php');
            exit;
        }

        $error = $respon->message;
    }
}

$events = $db->send_query(
    "SELECT id, name
     FROM events
     ORDER BY event_date, event_time"
)->data;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Ticket</title>
</head>

<body>

<h1>Tambah Ticket</h1>

<?php if ($error): ?>

    <p style="color: red;">
        <?= htmlspecialchars($error) ?>
    </p>

<?php endif; ?>

<form method="POST">

    <div>
        <label for="event_id">Event</label>
        <select name="event_id" id="event_id" required>

            <option value="">
                -- Pilih Event --
            </option>

            <?php foreach ($events as $event): ?>

                <option value="<?= $event['id'] ?>">
                    <?= htmlspecialchars($event['name']) ?>
                </option>

            <?php endforeach; ?>

        </select>
    </div>

    <br>

    <div>
        <label for="name">Nama Ticket</label>
        <input
            type="text"
            name="name"
            id="name"
            maxlength="100"
            required
        >
    </div>

    <br>

    <div>
        <label for="price">Harga</label>
        <input
            type="number"
            name="price"
            id="price"
            min="0"
            step="0.01"
            required
        >
    </div>

    <br>

    <div>
        <label for="stock">Stok</label>
        <input
            type="number"
            name="stock"
            id="stock"
            min="0"
            required
        >
    </div>

    <br>

    <button type="submit">
        Simpan
    </button>

    <a href="index.php">
        Kembali
    </a>

</form>

</body>
</html>